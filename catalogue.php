<?php require_once __DIR__.'/includes/bootstrap.php'; $q=trim($_GET['q']??''); $cat=(int)($_GET['category']??0);
 $sql="SELECT p.*,c.name category_name,u.name farmer_name FROM produce p JOIN categories c ON c.id=p.category_id 
 JOIN users u ON u.id=p.farmer_id WHERE p.status='approved' AND p.quantity>0"; $args=[]; 
 if($q!==''){ $sql.=" AND (p.name LIKE ? OR p.location LIKE ? OR u.name LIKE ?)"; $like="%$q%"; array_push($args,$like,$like,$like); }
  if($cat){$sql.=" AND c.id=?";$args[]=$cat;} $sql.=" ORDER BY p.created_at DESC";
   $st=$pdo->prepare($sql);$st->execute($args);$items=$st->fetchAll();$cats=$pdo->query 
  ('SELECT * FROM categories ORDER BY name')->fetchAll(); include __DIR__.'/includes/header.php'; ?>
<section class="page-banner"><span class="eyebrow">THE PRODUCE MARKET</span><h1>Browse fresh produce</h1>
<p>Find what you need from farmers near you.</p></section>
<section class="section"><form class="filters" method="get">
    <input name="q" value="<?= e($q) ?>" placeholder="Search produce, farmer or location"><select name="category"><option value="0">All categories</option>
    <?php foreach($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $cat===$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
    <?php endforeach; ?></select><button class="btn" type="submit">Search</button></form>
    <div class="cards"><?php foreach($items as $p): ?><article class="product-card"><div class="product-photo"><?php if($p['photo']): ?><img src="<?= e($p['photo']) ?>" alt="<?= e($p['name']) ?>"><?php else: ?>
        <span>🌿</span><?php endif; ?></div><div class="product-info"><small><?= e($p['category_name']) ?> · <?= e($p['location']) ?></small><h3><?= e($p['name']) ?></h3><p class="farmer">By <?= e($p['farmer_name']) ?></p><div class="price-row"><strong>KSh <?= number_format((float)$p['price'],2) ?></strong><span>/ <?= e($p['unit']) ?></span></div><a class="btn btn-small" href="product.php?id=<?= (int)$p['id'] ?>">View details</a></div></article><?php endforeach; ?><?php if(!$items): ?><div class="empty">No matching produce. Try another search.</div><?php endif; ?></div></section><?php include __DIR__.'/includes/footer.php'; ?>