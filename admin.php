<?php
require_once __DIR__.'/includes/bootstrap.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'approve_order' || $action === 'reject_order') {
        try {
            $pdo->beginTransaction();
            $st = $pdo->prepare("SELECT * FROM orders WHERE id=? AND status='pending_approval' FOR UPDATE");
            $st->execute([$id]);
            $order = $st->fetch();
            if (!$order) throw new Exception('This order is no longer awaiting approval.');

            if ($action === 'approve_order') {
                $st = $pdo->prepare('SELECT oi.produce_id, oi.quantity, p.quantity available, p.status produce_status, p.name FROM order_items oi JOIN produce p ON p.id=oi.produce_id WHERE oi.order_id=? FOR UPDATE');
                $st->execute([$id]);
                $items = $st->fetchAll();
                if (!$items) throw new Exception('This order has no items.');
                foreach ($items as $item) {
                    if ($item['produce_status'] !== 'approved' || (int)$item['quantity'] > (int)$item['available']) {
                        throw new Exception('Cannot approve order: '.$item['name'].' is no longer available in the requested quantity.');
                    }
                }
                foreach ($items as $item) {
                    $upd = $pdo->prepare('UPDATE produce SET quantity=quantity-? WHERE id=?');
                    $upd->execute([(int)$item['quantity'], (int)$item['produce_id']]);
                }
                $st = $pdo->prepare("UPDATE orders SET status='placed' WHERE id=?");
                $st->execute([$id]);
                flash('success', 'Order #'.$id.' approved. Stock has been reserved and deducted.');
            } else {
                $st = $pdo->prepare("UPDATE orders SET status='rejected' WHERE id=?");
                $st->execute([$id]);
                flash('success', 'Order #'.$id.' rejected. No stock was deducted.');
            }
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            flash('error', $e instanceof PDOException ? 'Unable to update the order. Please try again.' : $e->getMessage());
        }
    } elseif ($action === 'approve' || $action === 'reject') {
        $st = $pdo->prepare('UPDATE produce SET status=? WHERE id=?');
        $st->execute([$action === 'approve' ? 'approved' : 'rejected', $id]);
        flash('success', 'Listing status updated.');
    } elseif ($action === 'status') {
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['confirmed','dispatched','delivered'], true)) {
            $st = $pdo->prepare("UPDATE orders SET status=? WHERE id=? AND status IN ('placed','confirmed','dispatched')");
            $st->execute([$status, $id]);
            flash('success', 'Order status updated.');
        }
    }
    redirect('admin.php');
}

$pending = $pdo->query("SELECT p.*,u.name farmer,c.name category_name FROM produce p JOIN users u ON u.id=p.farmer_id JOIN categories c ON c.id=p.category_id WHERE p.status='pending' ORDER BY p.created_at")->fetchAll();
$pendingOrders = $pdo->query("SELECT o.*,u.name buyer FROM orders o JOIN users u ON u.id=o.buyer_id WHERE o.status='pending_approval' ORDER BY o.created_at ASC")->fetchAll();
$orders = $pdo->query('SELECT o.*,u.name buyer FROM orders o JOIN users u ON u.id=o.buyer_id ORDER BY o.created_at DESC LIMIT 100')->fetchAll();
$stats = [
    'users' => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'produce' => $pdo->query("SELECT COUNT(*) FROM produce WHERE status='approved'")->fetchColumn(),
    'orders' => $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn()
];
include __DIR__.'/includes/header.php';
?>
<section class="page-banner"><span class="eyebrow">ADMINISTRATION</span><h1>Agribusiness dashboard</h1><p>Review farmer listings, approve or reject buyer orders, and track fulfilment.</p></section>
<section class="section">
<div class="stats"><div class="stat"><small>Registered users</small><b><?= $stats['users'] ?></b></div><div class="stat"><small>Approved listings</small><b><?= $stats['produce'] ?></b></div><div class="stat"><small>Total orders</small><b><?= $stats['orders'] ?></b></div></div>
<?= show_flash() ?>
<h2>Pending produce approvals</h2>
<div class="listing-stack">
<?php foreach ($pending as $p): ?><article class="panel listing"><div><h3><?= e($p['name']) ?></h3><p><?= e($p['category_name']) ?> · <?= e($p['farmer']) ?> · <?= e($p['location']) ?></p><p>KSh <?= number_format($p['price'],2) ?> / <?= e($p['unit']) ?> · Qty <?= (int)$p['quantity'] ?></p></div><div class="listing-actions"><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn btn-small" name="action" value="approve">Approve</button><button class="btn btn-small btn-danger" name="action" value="reject">Reject</button></form></div></article><?php endforeach; ?>
<?php if (!$pending): ?><p class="muted">No pending listings.</p><?php endif; ?>
</div>

<h2 class="mt">Buyer orders awaiting approval</h2>
<div class="listing-stack">
<?php foreach ($pendingOrders as $o): ?>
<article class="panel listing">
<div><h3>Order #<?= (int)$o['id'] ?> · <?= e($o['buyer']) ?></h3><p><?= e($o['created_at']) ?> · Total: KSh <?= number_format((float)$o['total'],2) ?></p>
<?php $its=$pdo->prepare('SELECT oi.quantity,oi.unit_price,p.name,p.unit FROM order_items oi JOIN produce p ON p.id=oi.produce_id WHERE oi.order_id=?'); $its->execute([$o['id']]); foreach($its->fetchAll() as $it): ?><p><?= e($it['name']) ?> — <?= (int)$it['quantity'] ?> <?= e($it['unit']) ?> × KSh <?= number_format((float)$it['unit_price'],2) ?></p><?php endforeach; ?>
</div><div class="listing-actions"><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$o['id'] ?>"><button class="btn btn-small" name="action" value="approve_order">Approve order</button><button class="btn btn-small btn-danger" name="action" value="reject_order" onclick="return confirm('Reject this order?')">Reject order</button></form></div>
</article>
<?php endforeach; ?>
<?php if (!$pendingOrders): ?><p class="muted">No buyer orders are waiting for approval.</p><?php endif; ?>
</div>

<h2 class="mt">Recent orders</h2><div class="panel table-wrap"><table><thead><tr><th>Order</th><th>Buyer</th><th>Date</th><th>Total</th><th>Status</th><th>Update</th></tr></thead><tbody>
<?php foreach ($orders as $o): ?><tr><td>#<?= (int)$o['id'] ?></td><td><?= e($o['buyer']) ?></td><td><?= e($o['created_at']) ?></td><td>KSh <?= number_format((float)$o['total'],2) ?></td><td><?= e(ucwords(str_replace('_',' ',$o['status']))) ?></td><td>
<?php if (in_array($o['status'], ['placed','confirmed','dispatched'], true)): ?><form method="post" class="inline-form"><?= csrf_field() ?><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= (int)$o['id'] ?>"><select name="status"><?php if($o['status']==='placed'): ?><option value="confirmed">Confirmed</option><?php endif; ?><?php if(in_array($o['status'],['placed','confirmed'],true)): ?><option value="dispatched">Dispatched</option><?php endif; ?><option value="delivered">Delivered</option></select><button class="btn btn-small">Update</button></form><?php elseif($o['status']==='delivered'): ?>Complete<?php else: ?>—<?php endif; ?>
</td></tr><?php endforeach; ?>
</tbody></table></div>
</section>
<?php include __DIR__.'/includes/footer.php'; ?>
