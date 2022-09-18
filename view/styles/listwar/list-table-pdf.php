<?php
    if(!isset($body)){
        exit;
    }

    if(!isset($header)){
        $header = [array_keys($body[0])];
    }else{
        if(isset($header[0][0]) && !is_array($header[0][0])){
            $header = [$header];
        }
    }
?>

<div class="text-dark"><hr></div>
<span class="fw-bold" style="font-size: 20px;">
    <?=$listname ?? '---'?> (<span class="text-success"><?=$members?></span>)
</span>
<div class="text-dark"><hr></div>
<table class="table table-dark table-striped">
    <caption>
        <?php if(isset($description) && !empty($description)):?>
            <span class="fs-4 fw-bold">Nota:</span> <?=$description?>
        <?php endif;?>
    </caption>
    <thead>
        <?php foreach($header as $values):?>
            <tr>
                <?php foreach($values as $value):?>
                    <th><?=$value?></th>
                <?php endforeach;?>
            </tr>
        <?php endforeach;?>
    </thead>
    <tbody>
        <?php foreach($body as $key => $rows):?>
            <tr>
                <?=$rows?>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>
