-- Run this once on an existing Agribusiness database to enable admin order approval.
USE Agribusiness;
ALTER TABLE orders
  MODIFY status ENUM('pending_approval','placed','confirmed','dispatched','delivered','rejected','cancelled')
  NOT NULL DEFAULT 'pending_approval';
