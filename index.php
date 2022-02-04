<?php 

require_once './core/.autoload.php'; 

Html::addMeta(['charset' => 'UTF-8']);
Html::addMeta(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);
Html::addMeta(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0']);

Html::setTitle(PROYECT_NAME);
Html::setIcon(Session::get('icon')??'');

Html::addStyle(['href' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', 'integrity' => 'sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3', 'crossorigin' => 'anonymous']);
Html::addStyle(['href' => asset('css/style.css')]);
Html::addStyle(['href' => 'https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css']);
Html::addStyle(['href' => 'https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css']);
Html::addStyle(['href' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css', 'integrity' => 'sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==', 'crossorigin' => 'anonymous', 'referrerpolicy' => 'no-referrer']);

Html::addScript(['src' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js', 'integrity' => 'sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==', 'crossorigin' => 'anonymous', 'referrerpolicy' => 'no-referrer']);
Html::addScript(['src' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', 'integrity' => 'sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p', 'crossorigin' => 'anonymous']);
Html::addScript(['src' => 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js', 'crossorigin' => 'anonymous']);
Html::addScript(['src' => asset('js/datatables-simple-demo.js')]);
Html::addScript(['src' => 'https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js']);
Html::addScript(['src' => asset('js/script.js')]);
Html::addScript(['src' => asset('js/demo/chart-area-demo.js')]);
Html::addScript(['src' => 'https://cdn.jsdelivr.net/npm/simple-datatables@latest', 'crossorigin' => 'anonymous']);

Html::OutPut();