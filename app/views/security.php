<?php $this->layout('layout', ['title' => 'Безопасность']) ?>


<main id="js-page-content" role="main" class="page-content mt-3">
    <div class="subheader">
        <h1 class="subheader-title">
            <i class="subheader-icon fal fa-lock"></i> Безопасность
        </h1>

    </div>
    <form action="/user/editsecurity" method="post">
        <div class="row">
            <div class="col-xl-6">
                <div id="panel-1" class="panel">
                    <div class="panel-container">
                        <div class="panel-hdr">
                            <h2>Обновление эл. адреса и пароля</h2>
                        </div>
                        <div class="panel-content">
                            <input type="hidden" value="<?=$user['id']?>" name="id">
                            <!-- email -->
                            <div class="form-group">
                                <label class="form-label" for="">Email</label>
                                <input type="email" class="form-control" value="<?=$user['email']?>" name="email">
                            </div>

                            <!-- password -->
                            <div class="form-group">
                                <label class="form-label" for="">Пароль</label>
                                <input type="password" class="form-control" name="password">
                            </div>

                            <!-- password confirmation-->
                            <!--                                <div class="form-group">-->
                            <!--                                    <label class="form-label" for="simpleinput">Подтверждение пароля</label>-->
                            <!--                                    <input type="password" id="simpleinput" class="form-control">-->
                            <!--                                </div>-->


                            <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                <button class="btn btn-warning waves-effect waves-themed" name="submit">Изменить</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</main>