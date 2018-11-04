{if $show_message}
    <div class="alert alert-success">Configuration updated successfully</div>
{/if}

<form method="POST">
    <div class="form-group col-sm-4">
        <label for="paid_status">Change paid order status to:</label>
        <select name="paid_status" class="form-control" id="paid_status">
            <option value="">Do not change</option>
            {html_options options=$orderStatuses selected=$config.paid_status}
        </select>
    </div>
    <div class="col-sm-12"></div>
    <div class="form-group col-sm-4">
        <label for="cancel_unpaid">Cancel unpaid orders on daily cron:</label>
        <input type="checkbox" name="cancel_unpaid" class="form-control" id="cancel_unpaid" value="1" {if $config.cancel_unpaid}checked{/if} />
    </div>
    <div class="col-sm-12"></div>
    <div class="form-group col-sm-4">
        <label for="admin">Admin user for API calls:</label>
        <select name="admin" class="form-control" id="admin">
            {html_options options=$admins selected=$config.admin}
        </select>
    </div>
    <div class="col-sm-12"></div>
    <div class="col-sm-4 text-center">
        <br /><br />
        <input value="Save" class="button btn btn-default" type="submit" />
    </div>
</form>

<form method="POST" id="manualForm" action="addonmodules.php?module=purge_unpaid_orders&action=manual">
    <div class="col-sm-12"></div>
    <div class="col-sm-4 text-center">
        <br /><br />
        <input type="submit" class="btn btn-danger" value="Manual Purge" />
    </div>
</form>

<script type="text/javascript">
    {literal}
        $(function() {
            $('#manualForm').submit(function () {
                if (confirm('Are you sure want to cancel all unpaid orders?')) {
                    return true;
                } else {
                    return false;
                }
            });
        });
    {/literal}
</script>