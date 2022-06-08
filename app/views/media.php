<?php $this->layout('layout', ['title' => 'Загрузить аватар']) ?>

<main id="js-page-content" role="main" class="page-content mt-3">
    <div class="subheader">
        <h1 class="subheader-title">
            <i class="subheader-icon fal fa-image"></i> Загрузить аватар
        </h1>

    </div>
    <form action="/user/uploadavatar" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?=$user['id']?>">
        <div class="row">
            <div class="col-xl-6">
                <div id="panel-1" class="panel">
                    <div class="panel-container">
                        <div class="panel-hdr">
                            <h2>Текущий аватар</h2>
                        </div>
                        <div class="panel-content">
                            <?= flash()->display()?>

                            <div class="form-group">
                                <img src="<?=$user['avatar'] ? '/uploads/'.$user['avatar'] : '/img/demo/avatars/avatar-m.png' ?>" alt="" class="rounded-circle img-responsive" width="200" height="200">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="">Выберите аватар</label>
                                <input type="file" class="form-control-file" name="avatar">
                            </div>


                            <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                <button type="submit" class="btn btn-warning waves-effect waves-themed">Загрузить</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>