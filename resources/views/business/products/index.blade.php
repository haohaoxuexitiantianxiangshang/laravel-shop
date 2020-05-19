@extends('layouts.app')
@section('title', '商品列表')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-body">
                    <div class="row products-list">
                        @foreach($products as $product)
                            <div class="col-12 product-item">
                                <div class="product-content">
                                    <div class="top">
                                        <div class="img">
                                                <img src="{{ $product->image_url }}" alt="">
                                        </div>
                                        <div class="title">
                                            <span>{{ $product->title }}</span>
                                        </div>
                                        <div class="skus">
                                            <label>选择</label>
                                            <div class="btn-group btn-group-toggle btn-group-vertical"
                                                 data-toggle="buttons">
                                                @foreach($product->skus as $sku)
                                                    <label
                                                        class="btn sku-btn"
                                                        data-price="{{ $sku->price }}"
                                                        data-stock="{{ $sku->stock }}"
                                                        data-toggle="tooltip"
                                                        title="{{ $sku->description }}"
                                                        data-placement="bottom">

                                                        <input type="radio" name="skus-{{$product->id}}"
                                                               autocomplete="off"
                                                               value="{{ $sku->id }}"> {{ $sku->title }}
                                                        <em>￥</em>{{ $product->price }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>
                                    <button class="btn btn-warning btn-lg btn-block btn-add-to-cart"
                                            value="{{$product->id}}" type="submit">加入购物车
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="float-right">{{ $products->render() }}</div>

                </div>
            </div>
        </div>
    </div>
    <div class="row" style="padding: 1rem;">
        <a href="/business/cart" class="btn btn-primary btn-lg btn-block">提交订单</a>
    </div>
@endsection
@section('scriptsAfterJs')
    <script>
        $(document).ready(function () {
            $('.sku-btn').click(function () {
                $('.price span').text($(this).data('price'));
            });


            // 加入购物车按钮点击事件
            $('.btn-add-to-cart').click(function () {
                skus_name = 'skus-' + $(this).val()
                // 请求加入购物车接口
                axios.post('{{ route('business.cart.add') }}', {
                    sku_id: $('label.active input[name=' + skus_name + ']').val(),
                    amount: 1,
                })
                    .then(function () { // 请求成功执行此回调
                        swal('加入购物车成功', '', 'success');
                    }, function (error) { // 请求失败执行此回调
                        if (error.response.status === 401) {

                            // http 状态码为 401 代表用户未登陆
                            swal('请先登录', '', 'error');

                        } else if (error.response.status === 422) {

                            // http 状态码为 422 代表用户输入校验失败
                            var html = '<div>';
                            _.each(error.response.data.errors, function (errors) {
                                _.each(errors, function (error) {
                                    html += error + '<br>';
                                })
                            });
                            html += '</div>';
                            swal({content: $(html)[0], icon: 'error'})
                        } else {

                            // 其他情况应该是系统挂了
                            swal('系统错误', '', 'error');
                        }
                    })
            });

        });
    </script>
@endsection
