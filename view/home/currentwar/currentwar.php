<div class="card">
    <div class="card-header">Guerra Actual</div>
    <div class="card-body">
        <?php if(isset($currentWar) && count($currentWar) > 0):?>
        
        <?php else:?>
            <div class="text-center">No hay guerra disponible.</div>
        <?php endif;?>
    </div>
</div>