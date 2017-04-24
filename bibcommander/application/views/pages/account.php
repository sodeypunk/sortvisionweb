<div class="container sortvision-container">
    <div class="row">
        <div class="col-md-3">
            <ul class="nav nav-pills nav-stacked admin-menu">
                <li class="active"><a href="#" data-target-id="Profile"><i class="fa fa-home fa-fw"></i>Profile</a></li>
                <li><a href="#" data-target-id="Activity"><i class="fa fa-file-o fa-fw"></i>Activity</a></li>
                <li><a href="#" data-target-id="Devices"><i class="fa fa-bar-chart-o fa-fw"></i>Devices</a></li>
                <li><a href="#" data-target-id="Messaging"><i class="fa fa-table fa-fw"></i>Messaging</a></li>
                <li><a href="#" data-target-id="InstallationJobs"><i class="fa fa-tasks fa-fw"></i>Installation Jobs</a></li>
                <li><a href="#" data-target-id="Settings"><i class="fa fa-cogs fa-fw"></i>Settings</a></li>
            </ul>
        </div>
        <div class="col-md-9 well admin-content" id="Profile">
            <div class="row">
                <div class="col-md-3">
                    Email
                </div>
                <div class="col-md-9">
                    <?php echo $id_user; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    Token
                </div>
                <div class="col-md-9">
                    <textarea class="form-control" rows="10"><?php echo $token; ?></textarea>
                </div>
            </div>
        </div>
        <div class="col-md-9 well admin-content" id="Activity">
            Pages
        </div>
        <div class="col-md-9 well admin-content" id="Devices">
            Charts
        </div>
        <div class="col-md-9 well admin-content" id="Messaging">
            Table
        </div>
        <div class="col-md-9 well admin-content" id="InstallationJobs">
            Forms
        </div>
        <div class="col-md-9 well admin-content" id="Settings">
            Settings
        </div>
    </div>
</div>

<script>
    $(document).ready(function()
    {
        var navItems = $('.admin-menu li > a');
        var navListItems = $('.admin-menu li');
        var allWells = $('.admin-content');
        var allWellsExceptFirst = $('.admin-content:not(:first)');

        allWellsExceptFirst.hide();
        navItems.click(function(e)
        {
            e.preventDefault();
            navListItems.removeClass('active');
            $(this).closest('li').addClass('active');

            allWells.hide();
            var target = $(this).attr('data-target-id');
            $('#' + target).show();
        });
    });
</script>
