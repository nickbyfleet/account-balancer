<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>

    <style>
        body {
            padding-top: 5rem;
        }
        .starter-template {
            padding: 3rem 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <main role="main" class="container">

        <div class="starter-template">
            <h1>Account Balancer</h1>
            <p class="lead">
                Put in your current account balances and desired balances
                <br />Then click <code>Calculate Movements</code>
            </p>
        </div>

        <form method="post">

            @csrf

            @if ($errors->any())
                <div class="row alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Some of the formatting looks weird here because of the way textareas deal with whitespace -->

            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="current_state">Current State</label>
                        <textarea class="form-control" id="current_state" name="current_state" rows="15">@if(isset($request) && $request->get('current_state')) {{$request->get('current_state')}} @else
[
  {
    "account_id": 1,
    "balance": 500000
  },
  {
    "account_id": 2,
    "balance": 200000
  },
  {
    "account_id": 3,
    "balance": 300000
  }
]
                            @endif
                        </textarea>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="desired_state">Desired State</label>
                        <textarea class="form-control" id="desired_state" name="desired_state" rows="15">@if(isset($request) && $request->get('desired_state')) {{$request->get('desired_state')}} @else
[
  {
    "account_id": 1,
    "balance": 900000
  },
  {
    "account_id": 2,
    "balance": 30000
  },
  {
    "account_id": 3,
    "balance": 70000
  }
]
                        @endif
                        </textarea>
                    </div>

                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-primary">Calculate Movements</button>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="result">Movements</label>
                        <textarea class="form-control" id="result" name="result" rows="15" readonly>{{ $movements ?? '' }}
                        </textarea>
                    </div>
                </div>
            </div>
        </form>


    </main><!-- /.container -->

</body>
</html>
