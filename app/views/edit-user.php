<?php $this->layout('layout', ['title' => 'Редактирование пользователя']) ?>

<main id="js-page-content" role="main" class="page-content mt-3">
    <div class="subheader">
        <h1 class="subheader-title">
            <i class="subheader-icon fal fa-plus-circle"></i> Редактировать
        </h1>
    </div>
    <form action="/user/edituser" method="post">
        <div class="row">
            <div class="col-xl-6">
                <div id="panel-1" class="panel">
                    <div class="panel-container">
                        <div class="panel-hdr">
                            <h2>Общая информация</h2>
                        </div>
                        <div class="panel-content">
                            <?= flash()->display()?>

                            <input type="hidden" name="id" value="<?=$user['id']?>">
                            <!-- username -->
                            <div class="form-group">
                                <label class="form-label" for="">Имя</label>
                                <input type="text" class="form-control" name="fullname" value="<?=$user['fullname']?>">
                            </div>

                            <!-- title -->
                            <div class="form-group">
                                <label class="form-label" for="">Место работы</label>
                                <input type="text" class="form-control" name="workplace" value="<?=$user['workplace']?>">
                            </div>

                            <!-- tel -->
                            <div class="form-group">
                                <label class="form-label" for="">Номер телефона</label>
                                <input type="text" class="form-control" name="phone" value="<?=$user['phone']?>">
                            </div>

                            <!-- address -->
                            <div class="form-group">
                                <label class="form-label" for="">Адрес</label>
                                <input type="text" class="form-control" name="adress" value="<?=$user['adress']?>">
                            </div>
                            <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                <button type="submit" class="btn btn-warning waves-effect waves-themed">Редактировать</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>