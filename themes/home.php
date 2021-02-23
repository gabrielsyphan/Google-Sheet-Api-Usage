<?php $v->layout("_theme.php") ?>

<div class="container">
    <div class="row">
        <div class="col-xl-12 pt-5">
                <div class="row">
                    <div class="col-sm-5">
                        <h3>Tickets</h3>
                        <p>Tickets cadastrados no Ostic</p>
                    </div>
                    <div class="col-sm-7 pt-5">
                        <div class="d-flex float-right">
                            <button class="ml-3 btn btn-primary" onclick="home()">
                                <span class="icon-home"></span>
                                Início
                            </button>
                            <button class="ml-3 btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                                <span class="icon-search"></span>
                                Buscar tickets
                            </button>
                            
                            <select class="form-control ml-3" name="selector" style="border-radius: 4px 0 0 4px !important;">
                                <?php if($sheets): foreach($sheets as $sheet): ?>
                                    <option value="<?= $sheet['sheet_id'] ?>"><?= $sheet['sheet_name'] ?></option>
                                <?php $aux++; endforeach; endif;?>
                            </select>
                            <button style="border-radius: 0 4px 4px 0 !important;" class="btn btn-primary ml-0" type="button" onclick="newSheet()"><span class="icon-add"></span></button>
                            
                            <button class="btn btn-primary ml-3" type="button" onclick="exportData()">
                                <span class="icon-download"></span>
                                Exportar
                            </button>
                            <button class="ml-3 btn btn-secondary" onclick="login()" title="Login">
                                <span class="icon-user"></span>
                            </button>
                            <button class="ml-3 btn btn-secondary" onclick="signout()">
                                <span class="icon-exit_to_app"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php if($tickets): ?>
                <hr>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Número do ticket</th>
                            <th scope="col">Data de criação</th>
                            <th scope="col">Equipe</th>
                            <th scope="col">Agente</th>
                            <th scope="col">Status</th>
                            <th scope="col">Atraso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $aux = 1; foreach($tickets as $ticket): ?>
                            <tr>
                                <th scope="row"><?= $aux ?></th>
                                <td><?= $ticket->number ?></td>
                                <td><?= date('d-m-Y', strtotime($ticket->created)); ?></td>
                                <td><?= ($ticket->team)? $ticket->team : ''; ?></td>
                                <td><?= $ticket->staff ?></td>
                                <td><?= $ticket->status ?></td>
                                <td><?= ($ticket->isoverdue == 0) ? "Não" : "Sim" ?></td>
                            </tr>
                        <?php $aux++; endforeach; ?>
                    </tbody>
                </table>
            <?php elseif(isset($aux)): ?>
                <h4 class="mt-5">Preencha os filtros acima para exibir algum ticket.</h4>
            <?php else:
                echo "Não há tickets";
            endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filtro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Agente</h5>
                <div class="d-flex mr-3">
                    <select class="form-control" name="agent" style="border-radius: 4px 0 0 4px !important;">
                        <option value="0">Não filtrar</option>
                        <?php if($agents): foreach($agents as $agent): ?>
                            <option value="<?= $agent->staff_id ?>"><?= $agent->firstname ?> <?= $agent->lastname ?></option>
                        <?php $aux++; endforeach; endif;?>
                    </select>
                </div>
                
                <h5>Grupo</h5>
                <div class="d-flex mr-3">
                    <select class="form-control" name="team" style="border-radius: 4px 0 0 4px !important;">
                        <option value="0">Não filtrar</option>
                        <?php if($teams): foreach($teams as $team): ?>
                            <option value="<?= $team->team_id ?>"><?= $team->name ?></option>
                        <?php $aux++; endforeach; endif;?>
                    </select>
                </div>
                
                <h5 class="mr-3">Periodo:</h5>
                <div class="d-flex mr-3">
                    <input id="date1" type="date" class="form-control w-50" style="border-radius: 4px 0 0 4px !important;">
                    <h5 class="ml-3 mr-3">a</h5>
                    <input id="date2" type="date" class="form-control w-50" style="border-radius: 4px 0 0 4px !important;">
                    <button class="btn btn-primary" style="border-radius: 0px 4px 4px 0 !important;" type="button" onclick="filter()">
                        <span class="icon-search"></span>
                    </button>
                </div>
                
                <h5 class="mr-3">Dia:</h5>
                <div class="d-flex mr-3">
                    <input id="inputDate" type="date" class="form-control w-100" style="border-radius: 4px 0 0 4px !important;">
                    <button class="btn btn-primary" style="border-radius: 0px 4px 4px 0 !important;" type="button" onclick="filterByDate()">
                        <span class="icon-search"></span>
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<?php $v->start("scripts") ?>
<script>
    function exportData() {
        const date = "<?= $date ?>";
        const sheet = $('select[name=selector] option').filter(':selected').val();
        const agent = "<?= $data['agent'] ?>";
        const team = "<?= $data['team'] ?>";
        
        if (sheet) {
            window.location.href =  "<?= url("exportData") ?>/" + date + "/" + sheet + "/" + agent + "/" + team;
        } else {
            swal({
                icon: "warning",
                title: "Ops!",
                text: "Você deve selecionar uma tabela.",
            });
        }
    }
    
    function home() {
        window.location.href =  "<?= url("") ?>";
    }
    
    function login() {
        window.location.href =  "<?= url("sheet") ?>";  
    }
    
    function filterByDate() {
        const date = $("#inputDate").val();
        const agent = $('select[name=agent] option').filter(':selected').val();
        const team = $('select[name=team] option').filter(':selected').val();
        
        if(date) {
            window.location.href = "<?= url("filter") ?>/" + date + "/" + agent + "/" + team;
        } else {
            swal({
                icon: "warning",
                title: "Ops!",
                text: "Informe uma data valida",
            });
        }
    }
    
    function newSheet() {
        const sheetName = prompt("Qual o nome da planilha?");
        if(sheetName) {
            window.location.href = "<?= url("createsheet") ?>/"+sheetName;
        } else {
            swal({
                icon: "warning",
                title: "Ops!",
                text: "O nome da planilha não pode ser vazio.",
            });
        }
    }
    
    function filter() {
        const date1 = $("#date1").val();
        const date2 = $("#date2").val();
        const agent = $('select[name=agent] option').filter(':selected').val();
        const team = $('select[name=team] option').filter(':selected').val();
        
        if(date1 && date2) {
            window.location.href = "<?= url("date") ?>/"+ date1 + "/" + date2 + "/" + agent + "/" + team;
        } else {
            swal({
                icon: "warning",
                title: "Ops!",
                text: "Informe uma data valida",
            });
        }
    }
    
    function signout() {
         window.location.href = "<?= url("signout") ?>";
    }
</script>
<?php $v->end(); ?>