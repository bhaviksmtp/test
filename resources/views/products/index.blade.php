@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Products</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('products.create') }}"> Create New Product </a>
            </div>
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Price</th>
            <th width="280px">Action</th>
        </tr>
	    @foreach ($products as $product)
	    <tr>
	        <td>{{ ++$i }}</td>
	        <td>{{ $product->name }}</td>
	        <td>{{ $product->price }}</td>
	        <td>
                <form action="{{ route('products.destroy',$product->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('products.show',$product->id) }}">Show</a>
                    @can('product-edit')
                    <a class="btn btn-primary" href="{{ route('products.edit',$product->id) }}">Edit</a>
                    @endcan


                    @csrf
                    @method('DELETE')
                    @can('product-delete')
                    <button type="submit" class="btn btn-danger">Delete</button>
                    @endcan
                </form>
                <button class="btn btn-warning buynow">Buy</button>
	        </td>
	    </tr>
	    @endforeach
    </table>

    {!! $products->links() !!}

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    {{-- <script src="https://checkout.stripe.com/checkout.js"></script>	 --}}
    <script src="https://js.stripe.com/v3/"></script>
    <script>
       $(document).ready(function() {
            $('.buynow').on('click',function(){
                // StripeCheckout.open({
                //     key: 		"<?php echo env('STRIPE_KEY'); ?>",
                //     amount: 	200,
                //     name: 		'Card Details',
                //     //image:       "{{url('/css/frontendV1/logo/new_logo-1.png')}}",
                //     description:'Please enter your credit or debit card details',
                //     panelLabel: 'Pay',
                //     token: 		'token',
                //     currency:   'USD',
                //     email: 'email@gmail.com'
                // });
                const stripe = Stripe("<?php echo env('STRIPE_KEY'); ?>");

                const elements = stripe.elements({
                    locale: 'de',
                    mode: 'payment',
                    amount: 1099,
                    currency: 'usd',
                })
    
            })
        })
        
    </script>

@endsection
