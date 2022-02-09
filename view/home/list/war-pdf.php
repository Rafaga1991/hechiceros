<div class="container-fluid px-4 pt-4">
    <div class="row">
        <div class="col"><a href="{!!_HOST_!!}">{!!_HOST_!!}</a></div>
        <div class="col text-center"><img src="{!!_ICON_URL!!}"><br>{!!__PROYECT_NAME__!!}</div>
        <div class="col text-end">{!!_DATE_!!}</div>
    </div>

    {!!members_war!!}
    
    <?php if(!empty($description)):?>
        <h2 class="text-muted mt-5">Descripci&oacute;n</h2>
        <span><?= $description ?></span>
    <?php endif;?>

    {!!members_wait!!}

    {!!members_break!!}
</div>