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

<style>
    html,body{
        margin: 0;
        padding: 0;
        font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    }

    table[container~=table]{
        width: 100%;
        height: auto;
    }

    th,caption{
        background-color: #2B2C2B;
        color: #FFF;
    }

    th, td,caption{
        padding: 10px 10px;
        border-color: transparent;
    }
</style>

<table container=table>
    <caption lists><?=$listname ?? ''?></caption>
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
            <tr bgcolor="<?=(($key%2) == 0)?'#F0F0F0':'#FFF'?>">
                <?=$rows?>
            </tr>
        <?php endforeach;?>
    </tbody>
    <?php if(isset($description)):?>
    <tfoot>
        <tr>
            <td>Nota: <?=empty($description)?'****':$description?></td>
        </tr>
    </tfoot>
    <?php endif;?>
</table>
<br><br><br>