ALTER TABLE `ajayfruits_db`.`master_sales_tbl` ADD INDEX `lot_no` (`m_sale_lot`, `m_sale_issueid`, `m_sale_customer`, `m_sale_spo`, `m_sale_date`) USING BTREE;

ALTER TABLE `master_sales_tbl` ADD `m_sale_voucher` VARCHAR(100) NOT NULL AFTER `m_sale_trackno`;
ALTER TABLE `master_sales_tbl` ADD `m_sale_branch` INT NOT NULL AFTER `m_sale_spo`, ADD INDEX `branch` (`m_sale_branch`);

ALTER TABLE `ajayfruits_db`.`master_purchase_tbl` ADD INDEX `lot_no` (`m_purcs_spo`, `m_purcs_date`, `m_purcs_available`, `m_purcs_suplier`, `m_purcs_lot`, `m_purcs_item`) USING BTREE;
ALTER TABLE `master_purchase_tbl` ADD `m_purcs_branch` INT NOT NULL AFTER `m_purcs_spo`, ADD INDEX `branch` (`m_purcs_branch`);
ALTER TABLE `master_purchase_tbl` ADD `m_purcs_billno` VARCHAR(100) NOT NULL AFTER `m_purcs_truckno`;