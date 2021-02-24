<?php defined('C5_EXECUTE') or die(_("Access Denied.")) ?>
<?php $this->requireAsset('core/notification'); ?>

<h3><?= count($joined) ?> Teilnehmer:
    <?php if ($isRegistered) { ?>
    <a href="mailto:<?= implode(",", array_column($joined, 'email')) ?>">
        <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pjxzdmcgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjQgMjQ7IiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAyNCAyNCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PGcgaWQ9ImluZm8iLz48ZyBpZD0iaWNvbnMiPjxwYXRoIGQ9Ik0yMS41LDExLjFsLTE3LjktOUMyLjcsMS43LDEuNywyLjUsMi4xLDMuNGwyLjUsNi43TDE2LDEyTDQuNiwxMy45bC0yLjUsNi43Yy0wLjMsMC45LDAuNiwxLjcsMS41LDEuMmwxNy45LTkgICBDMjIuMiwxMi41LDIyLjIsMTEuNSwyMS41LDExLjF6IiBpZD0ic2VuZCIvPjwvZz48L3N2Zz4="
            alt="emailto" width="20px" height="20px" />
    </a>
    <?php } ?>
</h3>
<div class="row">
    <?php foreach ($joined as $join) { ?>
    <div class="col-xs-6 col-sm-3 col-md-2">
        <div class="thumbnail">
            <img src="<?= $join["avatar"] ?>" width="50px" height="50px" alt="avatar" class="img-circle" />
            <div class="caption">
                <h3 class="text-center">
                    <?= $join["name"] ?>
                    <?php if ($join["comment"] != "") { ?>
                    <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pjxzdmcgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjQgMjQ7IiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAyNCAyNCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PGcgaWQ9ImluZm8iLz48ZyBpZD0iaWNvbnMiPjxwYXRoIGQ9Ik0yMCwxSDRDMS44LDEsMCwyLjgsMCw1djEwYzAsMi4yLDEuOCw0LDQsNHYzYzAsMC45LDEuMSwxLjMsMS43LDAuN0w5LjQsMTlIMjBjMi4yLDAsNC0xLjgsNC00VjUgICBDMjQsMi44LDIyLjIsMSwyMCwxeiBNMTQsMTNIOGMtMC42LDAtMS0wLjQtMS0xYzAtMC42LDAuNC0xLDEtMWg2YzAuNiwwLDEsMC40LDEsMUMxNSwxMi42LDE0LjYsMTMsMTQsMTN6IE0xNiw5SDggICBDNy40LDksNyw4LjYsNyw4YzAtMC42LDAuNC0xLDEtMWg4YzAuNiwwLDEsMC40LDEsMUMxNyw4LjYsMTYuNiw5LDE2LDl6IiBpZD0ibWVzc2FnZSIvPjwvZz48L3N2Zz4="
                        alt="comment" width="20px" height="20px" data-toggle="tooltip" data-placement="bottom"
                        title="<?= $join["comment"] ?>" onclick="showComment(this)" />
                    <?php } ?>
                </h3>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<script>
function showComment(e) {
    console.log(e.title);
    ConcreteAlert.dialog(
        'Kommentar',
        '<p>' + e.title + '</p>'
    );
}
</script>

<?php if ($isRegistered) { ?>
<?php if ($hasJoined) { ?>
<a class="btn btn-danger" href="<?php echo $view->action(
                                            'disjoin',
                                            Core::make('token')->generate('join')
                                        ) ?>">Nicht Teilnehmen
</a>
<button type="button" class="btn btn-primary" onclick="comment()">
    Kommentar
</button>
<div id="modal" style="display:none">
    <form action="<?php echo $view->action(
                                'comment',
                                Core::make('token')->generate('join')
                            ) ?>" method="post">
        <textarea class="form-control" rows="3" name="comment"
            placeholder="Kommentar"><?= $join["comment"] ?></textarea>
        <br />
        <button type="submit" class="btn btn-primary">Speichern</button>
    </form>
</div>
<script>
function comment() {
    ConcreteAlert.dialog(
        'Kommentar',
        $('#modal').html()
    );
}
</script>
<?php } else { ?>
<a class="btn btn-success" href="<?php echo $view->action(
                                                'join',
                                                Core::make('token')->generate('join')
                                            ) ?>">Teilnehmen
</a>
<?php } ?>
<?php } ?>