<header class="row bg-dark text-white">
    <div class="col py-3">
        <div class="text-center" style="width: 10rem;">
            <a href="<?=$host?>" style="text-decoration: none;" class="text-white">
                <img src="<?=$logo?>" width="50%" alt="">
                <br>
                <span><?=$name?></span>
            </a>
        </div>
    </div>
    
    <div class="col text-center position-relative">
        <div class="position-absolute">
            <img src="<?=$classofclans?>" alt="" width="45%">
        </div>
        <div class="position-absolute bg-dark" style="width: 100%;height: 100%;z-index:5;opacity: .8;"></div>
        <div class="position-absolute" style="width: 100%;z-index:5;">
            <div class="row position-relative pt-2">
                <div class="col">
                    <img src="<?=$king->image?>" alt="" width="50%">
                </div>
                <div class="col">
                    <span class="fs-5">
                        <?=$king->name?>
                    </span>
                    <br>
                    <span class="fw-bold"><?=$king->role?></span>
                </div>
                <div class="col">
                    <img src="<?=$king->townHallLevel?>" alt="" width="50%">
                </div>
            </div>
        </div>
    </div>
    
    <div class="col text-end py-3">
        <div>
            <span><?=$date?></span>
        </div>
        <div>
            <span><?=$time?></span><br>
        </div>
        <br>
        <div>
            <a href="<?=$list_war?>" class="text-white">Lista de Guerra</a>
        </div>
    </div>
</header>