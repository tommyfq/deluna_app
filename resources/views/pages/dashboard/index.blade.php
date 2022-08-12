<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @include('_includes.alert')
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="card gradient-2">
                                <div class="card-body">
                                    <h3 class="card-title text-white">Total Order</h3>
                                    <div class="d-inline-block">
                                        <h2 class="text-white">Rp. {{$total_order ? number_format($total_order,2,",",".") : '0'}}</h2>
                                    </div>
                                    <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card gradient-3">
                                <div class="card-body">
                                    <h3 class="card-title text-white">Total Order Today</h3>
                                    <div class="d-inline-block">
                                        <h2 class="text-white">Rp. {{$total_order_today ? number_format($total_order_today,2,",",".") : '0'}}</h2>
                                        <p class="text-white mb-0">{{date('Y-m-d')}}</p>
                                    </div>
                                    <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card gradient-1">
                                <div class="card-body">
                                    <h3 class="card-title text-white">Products Available</h3>
                                    <div class="d-inline-block">
                                        <h2 class="text-white">{{$total_product}}</h2>
                                    </div>
                                    <span class="float-right display-5 opacity-5"><i class="fa fa-shopping-cart"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>