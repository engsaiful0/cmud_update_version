<div class="clearfix">
    <p class="text-muted">
        Showing <?php echo $voucher_total > 0 ? $voucher_offset + 1 : 0; ?>
        to <?php echo $voucher_offset + count($voucherlist); ?>
        of <?php echo $voucher_total; ?> transactions
    </p>
    <?php echo $pagination_links; ?>
</div>
