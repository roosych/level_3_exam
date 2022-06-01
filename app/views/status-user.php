<?php $this->layout('layout', ['title' => 'Статус пользователя']) ?>


<main id="js-page-content" role="main" class="page-content mt-3">
    <div class="subheader">
        <h1 class="subheader-title">
            <i class="subheader-icon fal fa-sun"></i> Установить статус
        </h1>

    </div>
    <form action="/user/setstatus" method="post">
        <div class="row">
            <div class="col-xl-6">
                <div id="panel-1" class="panel">
                    <div class="panel-container">
                        <div class="panel-hdr">
                            <h2>Установка текущего статуса</h2>
                        </div>
                        <div class="panel-content">
                            <?= flash()->display()?>
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- status -->
                                    <div class="form-group">
                                        <input type="hidden" value="<?=$user['id']?>" name="id">
                                        <label class="form-label" for="example-select">Выберите статус</label>
                                        <select class="form-control" name="available_status">
                                            <option value=""></option>
                                            <?php foreach ($statuses as $status):?>
                                                <option value="<?=$status['value']?>" <?=$user['available_status'] == $status['value'] ? 'selected' : '' ?> ><?=$status['title']?></option>
                                            <?endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                    <button type="submit" class="btn btn-warning waves-effect waves-themed">Set Status</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</main>