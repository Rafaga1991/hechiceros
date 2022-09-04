<style>
    html,body{
        margin: 0px;
        padding: 0px;
    }

    footer{
        width: 100%;
        text-align: center;
        position: absolute;
        bottom: 0;
    }

    [content], footer{
        background-color: #363836;
        color: white;
        padding: 20px;
    }

    [lists]{
        padding: 25px 10px;
        background-color: #F0F0F0;
        color: #363836;
        font-weight: bold;
        font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    }

    [lists]>span{
        color: green;
    }

    [text~=center]{
        text-align: center;
    }

    [text~=upper]{
        text-transform: uppercase;
    }

    [text~=lower]{
        text-transform: lowercase;
    }

    [fs~=1]{
        font-size: 25px;
    }

    [fs~=2]{
        font-size: 20px;
    }

    [fs~=3]{
        font-size: 15px;
    }

    [fs~=4]{
        font-size: 10px;
    }

    [fs~=5]{
        font-size: 5px;
    }
</style>

<div content>
    <table container=table>
        <tr>
            <td>
                <div style="width: 80%;" text='center'>
                    <a href="<?=$host?>" style="text-decoration: none;color: white;" text="center">
                        <img src="<?=$logo?>" style="width: 70%;" alt=""> <br>
                        <b><?=$name?></b>
                    </a>
                </di>
            </td>
            <td>
                <?php if($king):?>
                <table container=table>
                    <tr text='center'>
                        <td>
                            <img src="<?=$king->townHallLevel?>" width="45px" alt="">
                        </td>
                        <td>
                            <b>
                                <?=$king->name?><br>
                                <span fs='4' text="lower">
                                    &nbsp;&nbsp;<?=$king->role?>
                                </span>
                            </b>
                        </td>
                        <td>
                            <img src="<?=$king->image?>" width="45px" alt="">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" text='center upper' fs='3'>Rey/na de las guerras</td>
                    </tr>
                </table>
                <?php endif;?>
            </td>
            <td style="text-align: right;">
                <?=$date?>
                <br>
                <?=$time?>
                <br><br>
                <a href="<?=$list_war?>" style="color: white;"><?=$listname?></a>
            </td>
        </tr>
    </table>
</div>