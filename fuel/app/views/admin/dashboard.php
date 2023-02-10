<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-6">
					<h6 class="h2 text-white d-inline-block mb-0">Dashboard</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
						</ol>
					</nav>
				</div>
                <div class="col-6 text-right d-none d-sm-block">
                    <h6 class="h4 text-white d-inline-block mb-0"><?php echo $date; ?></h6>
                </div>
            </div>
            <!-- CARD STATS -->
            <div class="row">
                <div class="col-xl-6 col-md-6">
                    <div class="card card-stats">
                        <!-- CARD BODY -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Ventas</h5>
                                    <span class="h2 font-weight-bold mb-0"><?php echo $sales_count; ?></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                                        <i class="ni ni-cart"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-md-6">
                    <div class="card card-stats">
                        <!-- CARD BODY -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Clientes</h5>
                                    <span class="h2 font-weight-bold mb-0"><?php echo $users_count; ?></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-blue text-white rounded-circle shadow">
                                        <i class="ni ni-paper-diploma"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-xl-6">
            <div class="card card-height">
                <div class="card-header">
                    <h6 class="text-uppercase text-muted ls-1 mb-1">Últimas</h6>
                    <h5 class="h3 mb-0">Venta</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if(!empty($sales)): ?>
                            <?php foreach($sales as $sale): ?>
                                <li class="list-group-item flex-column align-items-start py-4 px-4">
                                    <div class="checklist-item checklist-item-primary">
                                        <div class="checklist-info">
                                            <h5 class="checklist-title mb-0"><?php echo Html::anchor('admin/ventas/info/'.$sale['id'], $sale['customer'].' - '.$sale['email']); ?></h5>
                                            <small><?php echo $sale['total'].' - '.$sale['type']; ?></small>
                                            <small><?php echo $sale['sale_date']; ?> </small>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card card-height">
                <div class="card-header">
                    <h6 class="text-uppercase text-muted ls-1 mb-1">Últimos</h6>
                    <h5 class="h3 mb-0">Clientes</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if(!empty($users)): ?>
                            <?php foreach($users as $user): ?>
                                <li class="list-group-item flex-column align-items-start py-4 px-4">
                                    <div class="checklist-item checklist-item-primary">
                                        <div class="checklist-info">
                                            <h5 class="checklist-title mb-0"><?php echo $user['username']; ?></h5>
                                            <small><?php echo $user['email']; ?></small><br>
                                            <small class="<?php echo ($user['connected'] == 'Conectado') ? 'bg-success' : 'bg-warning'; ?>"><?php echo $user['connected']; ?></small><br>
                                            <small><?php echo $user['updated_at']; ?></small><br>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
