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


ALTER TABLE master_purchase_tbl
  ADD COLUMN m_purcs_type TINYINT DEFAULT 1 COMMENT '1=Purchase, 2=Transfer',
  ADD COLUMN m_purcs_ref_lot INT NULL COMMENT 'source m_purcs_id when type=2 (transfer)',
  ADD COLUMN m_purcs_from_branch INT NULL COMMENT 'source branch on a transfer row';

-- Security fix: widen password columns to fit bcrypt hashes (60 chars) ahead of
-- migrating from plaintext password storage to password_hash()/password_verify().
ALTER TABLE `master_users_tbl` MODIFY `m_user_password` VARCHAR(255) NOT NULL;
ALTER TABLE `master_customer_tbl` MODIFY `m_cust_password` VARCHAR(255) NOT NULL;
ALTER TABLE `application_settings` MODIFY `date_lock_password` VARCHAR(255) NULL;
-- ---------------------------------------------------------------------------
-- BUG-017: the mobile API trusted a caller-supplied user_id with no
-- authentication. Tokens are now issued by Api_Controller::user_login() and
-- required by every other endpoint.
--
-- Deploy note: apply this BEFORE deploying the code, and ship the mobile app
-- update that sends the token in the same window - older app builds will stop
-- working once the code is live.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `api_tokens` (
  `id`         BIGINT(20)   NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT(20)   NOT NULL,
  `token`      CHAR(64)     NOT NULL,
  `created_at` DATETIME     NOT NULL,
  `expires_at` DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_api_tokens_token` (`token`),
  KEY `ix_api_tokens_user` (`user_id`),
  KEY `ix_api_tokens_expiry` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------------------
-- BUG-005: master_admin_tbl holds PLAINTEXT passwords (e.g. 'ajayfruits001@',
-- '12345678').
--
-- Nothing in application/ references this table any more - logins go through
-- master_users_tbl, which uses password_hash()/password_verify(). The rows are
-- therefore dead data that only carries risk.
--
-- Left commented out because dropping data is not reversible: confirm the table
-- is genuinely unused in your environment, take a backup, then run ONE of these.
--
-- Option A - keep the rows but destroy the credentials:
--   UPDATE `master_admin_tbl` SET `m_admin_pass` = '';
--
-- Option B - remove the table entirely:
--   DROP TABLE `master_admin_tbl`;
--
-- NOTE: these passwords are also in git history. If they were ever reused
-- anywhere else, change them there too.
-- ---------------------------------------------------------------------------

-- ---------------------------------------------------------------------------
-- BUG-006: sales_list range filters used DATE_FORMAT(m_sale_date, ...) which
-- could not use an index. The model now compares the DATE column directly;
-- this index supports the common "date range within a branch" filter.
-- ---------------------------------------------------------------------------
ALTER TABLE `master_sales_tbl` ADD INDEX `ix_sales_branch_date` (`m_sale_branch`, `m_sale_date`);
ALTER TABLE `master_recieved_tbl` ADD INDEX `ix_recvd_branch_date` (`m_recvd_branch`, `m_recvd_date`);
ALTER TABLE `master_purchase_tbl` ADD INDEX `ix_purcs_branch_date` (`m_purcs_branch`, `m_purcs_date`);

-- ---------------------------------------------------------------------------
-- Branch backfill inconsistency (found during the BUG-003 audit).
--
-- The branch columns above were added with two different defaults:
--   m_sale_branch / m_purcs_branch  -> NOT NULL (default 0)  => existing rows = 0
--   every other *_branch column     -> NOT NULL DEFAULT 1     => existing rows = 1
--
-- So head-office history is branch 0 in sales/purchase and branch 1 everywhere
-- else, and neither 0 nor 1 is a real type-9 branch account. Audited counts on
-- the live database:
--   master_recieved_tbl  74295 rows at branch 1
--   staff_itemissue_tbl  21471
--   master_expenses_tbl   3836
--   master_payment_tbl    2480
--   master_voucher_tbl    1589
--   master_customer_tbl    534
--   master_sales_tbl / master_purchase_tbl: 0 affected (already branch 0)
--
-- This is NOT the BUG-003 corruption pattern (which would show scattered user
-- ids); it is the original migration's default. It is harmless while ordinary
-- users are unscoped, but it means a type-9 branch user will not match these
-- rows, and new head-office rows now write 0 while the old ones stay 1.
--
-- Normalise head office to 0 when you are ready. Take a backup first.
--   UPDATE `master_recieved_tbl`   SET `m_recvd_branch`   = 0 WHERE `m_recvd_branch`   = 1;
--   UPDATE `master_payment_tbl`    SET `m_payment_branch` = 0 WHERE `m_payment_branch` = 1;
--   UPDATE `master_voucher_tbl`    SET `m_voucher_branch` = 0 WHERE `m_voucher_branch` = 1;
--   UPDATE `staff_itemissue_tbl`   SET `si_issue_branch`  = 0 WHERE `si_issue_branch`  = 1;
--   UPDATE `master_customer_tbl`   SET `m_cust_branch`    = 0 WHERE `m_cust_branch`    = 1;
--   UPDATE `master_expenses_tbl`   SET `m_exp_branch`     = 0 WHERE `m_exp_branch`     = 1;
-- ---------------------------------------------------------------------------

-- ---------------------------------------------------------------------------
-- Superadmin "view password" feature: staff/branch login passwords stay
-- bcrypt-hashed in `m_user_password` for actual login (unchanged). This new
-- column carries a second, reversibly-encrypted copy (AES-256-CBC, key in
-- application/config/integrations.php:password_enc_key) that only superadmin
-- can decrypt, via Accounts::view_password().
--
-- Existing rows have NULL here until each user's password is next changed -
-- there is no plaintext left anywhere to backfill from.
-- ---------------------------------------------------------------------------
ALTER TABLE `master_users_tbl` ADD COLUMN `m_user_password_enc` VARCHAR(255) NULL AFTER `m_user_password`;

-- ---------------------------------------------------------------------------
-- BUG-028: branch ownership normalised onto Branch 1 (m_user_id 141).
--
-- APPLIED to the local DB on 2026-08-02. Backup taken first:
--   assets/database_backup/ajayfruits_db_pre_branch_migration_2026-08-02_140604.sql
--
-- Rows carried two conflicting conventions and neither matched a selectable
-- branch, so the superadmin branch filter returned nothing for Head Office and
-- Branch 2, and only 5 purchase lots for Branch 1:
--   * legacy 1 - the original ALTER DEFAULT, written before any branch record
--                existed (1 is the Super Admin account id, NOT a branch)
--   * 0        - "head office", written by the newer insert code
-- Real branches are 141 (Branch 1) and 142 (Branch 2). Per the owner's
-- decision all historical data belongs to Branch 1, so both conventions were
-- collapsed onto 141 - keeping a customer and their sales/receipts/payments on
-- the same branch so ledgers and balances reconcile under the filter.
--
-- This supersedes the older "normalise head office to 0" note further up in
-- this file - do NOT run those UPDATEs, they map the other way.
-- ---------------------------------------------------------------------------
UPDATE `master_custgroup_tbl`  SET `m_custgrp_branch` = 141 WHERE `m_custgrp_branch` = 1;
UPDATE `master_customer_tbl`   SET `m_cust_branch`    = 141 WHERE `m_cust_branch`    = 1;
UPDATE `master_group_tbl`      SET `m_group_branch`   = 141 WHERE `m_group_branch`   = 1;
UPDATE `master_itemgroup_tbl`  SET `m_itgrp_branch`   = 141 WHERE `m_itgrp_branch`   = 1;
UPDATE `master_item_tbl`       SET `m_item_branch`    = 141 WHERE `m_item_branch`    = 1;
UPDATE `master_payment_tbl`    SET `m_payment_branch` = 141 WHERE `m_payment_branch` = 1;
UPDATE `master_recieved_tbl`   SET `m_recvd_branch`   = 141 WHERE `m_recvd_branch`   = 1;
UPDATE `master_voucher_tbl`    SET `m_voucher_branch` = 141 WHERE `m_voucher_branch` = 1;
UPDATE `staff_itemissue_tbl`   SET `si_issue_branch`  = 141 WHERE `si_issue_branch`  = 1;
UPDATE `master_expenses_tbl`   SET `m_exp_branch`     = 141 WHERE `m_exp_branch`     = 1;
UPDATE `master_sales_tbl`      SET `m_sale_branch`    = 141 WHERE `m_sale_branch`    = 0;
UPDATE `master_purchase_tbl`   SET `m_purcs_branch`   = 141 WHERE `m_purcs_branch`   = 0;

-- Only operational accounts move. m_user_type 9 (the Branch 1 / Branch 2
-- records that DEFINE the branches) and 8 (Super Admin) stay on head office -
-- reassigning type 9 would make Branch 2's own record claim it is in Branch 1.
UPDATE `master_users_tbl` SET `m_user_branch` = 141
  WHERE `m_user_branch` = 1 AND `m_user_type` NOT IN (8, 9);
UPDATE `master_users_tbl` SET `m_user_branch` = 0 WHERE `m_user_type` = 8;

-- Stop new rows landing on the stale legacy value: an insert that omits the
-- branch now falls back to head office (0) instead of 1.
ALTER TABLE `master_custgroup_tbl` MODIFY `m_custgrp_branch` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `master_customer_tbl`  MODIFY `m_cust_branch`    int(11) NOT NULL DEFAULT 0;
ALTER TABLE `master_expenses_tbl`  MODIFY `m_exp_branch`     int(11) NOT NULL DEFAULT 0;
ALTER TABLE `master_group_tbl`     MODIFY `m_group_branch`   int(11) NOT NULL DEFAULT 0;
ALTER TABLE `master_itemgroup_tbl` MODIFY `m_itgrp_branch`   int(11) NOT NULL DEFAULT 0;
ALTER TABLE `master_item_tbl`      MODIFY `m_item_branch`    int(11) NOT NULL DEFAULT 0;
ALTER TABLE `master_payment_tbl`   MODIFY `m_payment_branch` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `master_recieved_tbl`  MODIFY `m_recvd_branch`   int(11) NOT NULL DEFAULT 0;
ALTER TABLE `master_users_tbl`     MODIFY `m_user_branch`    int(11) NOT NULL DEFAULT 0;
ALTER TABLE `master_voucher_tbl`   MODIFY `m_voucher_branch` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `staff_itemissue_tbl`  MODIFY `si_issue_branch`  int(11) NOT NULL DEFAULT 0;

-- Follow-up (F-06): these two were missed in the DEFAULT list above. They were
-- NOT NULL with no default at all, so an insert omitting the branch would error
-- rather than fall back to head office. Latent today - every writer sets the
-- column explicitly - but inconsistent with the other branch columns.
ALTER TABLE `master_sales_tbl`    MODIFY `m_sale_branch`  int(11) NOT NULL DEFAULT 0;
ALTER TABLE `master_purchase_tbl` MODIFY `m_purcs_branch` int(11) NOT NULL DEFAULT 0;

-- ============================================================================
-- 2026-08-06 - Report performance indexes (BUG-L04)
--
-- Reports/cust_blncrate_report and Reports/turck_report both ran past PHP's
-- max_execution_time (120s) and returned HTTP 500.
--
-- Root cause was two-part. The application side (per-customer / per-purchase
-- query loops) is fixed in Report_model. The storage side is here: the balance
-- queries filter master_sales_tbl by m_sale_customer, but the only composite
-- index on that table starts with m_sale_lot, so the leftmost-prefix rule made
-- it unusable and every lookup scanned all 87,895 rows. master_recieved_tbl
-- (74,295 rows) and master_voucher_tbl had nothing but a PRIMARY KEY.
--
-- Purely additive - no data or column definitions change.
-- Measured effect: crate balance report 120s timeout -> 5.9s,
--                  truck report 120s timeout -> 7.1s over full history.
-- ============================================================================
ALTER TABLE `master_sales_tbl`
  ADD INDEX `idx_sale_cust_date` (`m_sale_customer`, `m_sale_date`);

ALTER TABLE `master_recieved_tbl`
  ADD INDEX `idx_recvd_cust`         (`m_recvd_customer`, `m_recvd_account`, `m_recvd_type`, `m_recvd_date`),
  ADD INDEX `idx_recvd_acct_cust_id` (`m_recvd_account`, `m_recvd_customer`, `m_recvd_id`),
  ADD INDEX `idx_recvd_branch`       (`m_recvd_branch`);

ALTER TABLE `master_voucher_tbl`
  ADD INDEX `idx_voucher_acct` (`m_voucher_accountid`, `m_voucher_account`, `m_voucher_type`, `m_voucher_status`, `m_voucher_date`);

-- ============================================================================
-- 2026-08-06 - Two-decimal money handling (NOT APPLIED - for review)
--
-- Display is now fixed in code: common_helper.php gained money2() and a
-- corrected IND_money_format(), and every money cell renders at two decimals.
-- The statements below address the *storage* side and are deliberately left
-- commented out, because rounding stored amounts changes financial records and
-- is your decision, not a formatting change.
--
-- Why it matters: every amount column is `double`, so values drift. Rows whose
-- stored value has more than two decimals, as of 2026-08-06:
--     master_purchase_tbl.m_purcs_total     12
--     master_users_tbl.m_user_balance        4
--     master_sales_tbl.m_sale_price          2
--     master_sales_tbl.m_sale_total          1
--     master_customer_tbl.m_cust_balance     1
--     master_voucher_tbl.m_voucher_amount    1
-- (m_cust_balance / m_user_balance are caches the balance reports rewrite, so
--  those two correct themselves the next time a balance report is run.)
--
-- Option A - round the stored values once, leaving the column types alone:
-- UPDATE master_purchase_tbl SET m_purcs_total   = ROUND(m_purcs_total, 2)   WHERE m_purcs_total   <> ROUND(m_purcs_total, 2);
-- UPDATE master_sales_tbl    SET m_sale_price    = ROUND(m_sale_price, 2)    WHERE m_sale_price    <> ROUND(m_sale_price, 2);
-- UPDATE master_sales_tbl    SET m_sale_total    = ROUND(m_sale_total, 2)    WHERE m_sale_total    <> ROUND(m_sale_total, 2);
-- UPDATE master_voucher_tbl  SET m_voucher_amount = ROUND(m_voucher_amount, 2) WHERE m_voucher_amount <> ROUND(m_voucher_amount, 2);
--
-- Option B - stop the drift at the source by moving money columns to DECIMAL,
-- which stores exact base-10 values. Bigger change: test first, and note that
-- MySQL will round existing values on conversion.
-- ALTER TABLE `master_sales_tbl`    MODIFY `m_sale_total`     decimal(12,2) NOT NULL DEFAULT 0.00;
-- ALTER TABLE `master_purchase_tbl` MODIFY `m_purcs_total`    decimal(12,2) NOT NULL DEFAULT 0.00;
-- ALTER TABLE `master_recieved_tbl` MODIFY `m_recvd_amount`   decimal(12,2) NOT NULL DEFAULT 0.00;
-- ALTER TABLE `master_voucher_tbl`  MODIFY `m_voucher_amount` decimal(12,2) NOT NULL DEFAULT 0.00;
-- ============================================================================

-- ---------------------------------------------------------------------------
-- Superadmin "view password" feature, date-lock half. The date-lock password
-- stays bcrypt-hashed in `date_lock_password` for Login::* verification
-- (unchanged). This column carries a second, reversibly-encrypted copy
-- (AES-256-CBC, key in application/config/integrations.php:password_enc_key)
-- that only superadmin can decrypt, via Welcome::view_date_lock_password().
--
-- Mirrors master_users_tbl.m_user_password_enc above. The existing row has
-- NULL here until the date password is next changed - there is no plaintext
-- left anywhere to backfill from.
--
-- APPLIED to the local dev DB (ajayfruits_db) on 2026-08-22.
-- ---------------------------------------------------------------------------
ALTER TABLE `application_settings` ADD COLUMN `date_lock_password_enc` VARCHAR(255) NULL AFTER `date_lock_password`;

-- ---------------------------------------------------------------------------
-- Balance correction for the two legacy transfer rows (m_purcs_id 2946, 2947,
-- 116,340.00 total, dated 2026-07-16).
--
-- They were written by an earlier version of the transfer feature, which copied
-- the source lot's supplier onto the transfer row AND skipped the credit to the
-- receiving branch. Today's Main_model::insert_transfer() credits only the
-- branch and never touches the supplier, so this cannot recur.
--
-- The effect is one amount wrong in two places:
--
--   master_users_tbl 140 (AJAY SONKAR -LALI FRUIS, supplier)
--       stored 418,980.00, should be 302,640.00
--       = 292,810.00 purchases + 9,830.00 bill expenses; the remaining
--         116,340.00 was never a purchase from them
--
--   master_users_tbl 141 (Branch 1)
--       stored 2,189.99, should be 118,529.99
--       = the branch received that stock and was never charged for it
--
-- Both figures then agree with what the ledgers already report:
-- Reports::supplier_cash_ledger for 140 closes at 302,640.00, and
-- Reports::branch_ledger for 141 closes at 118,529.99, because
-- Report_model reads the transfer rows themselves rather than these caches.
--
-- The transfer rows are NOT deleted - the stock movement they record really
-- happened. Only the two cached balances are corrected.
--
-- NOT APPLIED - review before running. Verify afterwards by opening the
-- supplier ledger for 140 and the branch ledger for 141 and checking each
-- closing balance matches the stored one.
-- ---------------------------------------------------------------------------
-- UPDATE `master_users_tbl` SET `m_user_balance` = `m_user_balance` - 116340 WHERE `m_user_id` = 140 AND `m_user_type` = 2;
-- UPDATE `master_users_tbl` SET `m_user_balance` = `m_user_balance` + 116340 WHERE `m_user_id` = 141 AND `m_user_type` = 9;
