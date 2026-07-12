ALTER TABLE `ajayfruits_db`.`master_sales_tbl` ADD INDEX `lot_no` (`m_sale_lot`, `m_sale_issueid`, `m_sale_customer`, `m_sale_spo`, `m_sale_date`) USING BTREE;

ALTER TABLE `master_sales_tbl` ADD `m_sale_voucher` VARCHAR(100) NOT NULL AFTER `m_sale_trackno`;
ALTER TABLE `master_sales_tbl` ADD `m_sale_branch` INT NOT NULL AFTER `m_sale_spo`, ADD INDEX `branch` (`m_sale_branch`);

ALTER TABLE `ajayfruits_db`.`master_purchase_tbl` ADD INDEX `lot_no` (`m_purcs_spo`, `m_purcs_date`, `m_purcs_available`, `m_purcs_suplier`, `m_purcs_lot`, `m_purcs_item`) USING BTREE;
ALTER TABLE `master_purchase_tbl` ADD `m_purcs_branch` INT NOT NULL AFTER `m_purcs_spo`, ADD INDEX `branch` (`m_purcs_branch`);
ALTER TABLE `master_purchase_tbl` ADD `m_purcs_billno` VARCHAR(100) NOT NULL AFTER `m_purcs_truckno`;


ALTER TABLE `application_settings`
ADD COLUMN `m_app_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_app_id`;

ALTER TABLE `master_custgroup_tbl`
ADD COLUMN `m_custgrp_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_custgrp_id`;

ALTER TABLE `master_customer_tbl`
ADD COLUMN `m_cust_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_cust_id`;

ALTER TABLE `master_designation_tbl`
ADD COLUMN `m_design_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_design_id`;

ALTER TABLE `master_expenses_tbl`
ADD COLUMN `m_exp_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_exp_id`;

ALTER TABLE `master_group_tbl`
ADD COLUMN `m_group_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_group_id`;

ALTER TABLE `master_itemgroup_tbl`
ADD COLUMN `m_itgrp_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_itgrp_id`;

ALTER TABLE `master_item_tbl`
ADD COLUMN `m_item_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_item_id`;

ALTER TABLE `master_payment_tbl`
ADD COLUMN `m_payment_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_payment_id`;

ALTER TABLE `master_permission_tbl`
ADD COLUMN `m_perm_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_perm_id`;

ALTER TABLE `master_recieved_tbl`
ADD COLUMN `m_recvd_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_recvd_id`;

ALTER TABLE `master_users_tbl`
ADD COLUMN `m_user_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_user_id`;

ALTER TABLE `master_user_permission_tbl`
ADD COLUMN `m_userperm_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_userperm_id`;

ALTER TABLE `master_voucher_tbl`
ADD COLUMN `m_voucher_branch` int(11) NOT NULL DEFAULT 1 AFTER `m_voucher_id`;

ALTER TABLE `shorten_urls`
ADD COLUMN `m_shorturl_branch` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `staff_itemissue_tbl`
ADD COLUMN `si_issue_branch` int(11) NOT NULL DEFAULT 1 AFTER `si_issue_id`;


INSERT INTO `master_users_tbl` (`m_user_id`, `m_user_branch`, `m_user_name`, `m_user_mobile`, `m_user_type`, `m_user_image`, `m_user_phoneno`, `m_user_remark`, `m_user_pan_no`, `m_user_accountno`, `m_user_design`, `m_user_state`, `m_user_city`, `m_user_address`, `m_user_adharno`, `m_user_trademark`, `m_user_contractPerd`, `m_user_group`, `m_user_opening`, `m_user_crateOP`, `m_user_balance`, `m_user_10bal`, `m_user_20bal`, `m_user_25bal`, `m_user_login_allow`, `m_user_loginid`, `m_user_password`, `m_user_status`, `m_user_added_on`, `m_user_added_by`, `m_user_updated_on`, `m_user_updated_by`) VALUES ('1', '1', 'Super Admin', '', '8', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '1', 'ajayfruits', 'ajayfruits001@', '1', '2026-06-22 20:22:15.000000', '1', '2026-06-22 20:22:15.000000', '1');