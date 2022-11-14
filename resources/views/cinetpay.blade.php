</<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SDK-LARAVEL</title>
</head>
<body>
    <div class="pt-5">
     <div class="container">
         <div class="row justify-content-center">

             @if(session()->has('info'))
                 <div class="alert alert-primary text-center" role="alert">
                    {{session('info')}}
                 </div>
             @endif
             <div class="col-md-9 text-center p-2">
                 <h1 class="text-center">SDK LARAVEL</h1>
             </div>
             <div class="col-md-4">
                 <form method="POST" action="">
                     @csrf
                     <div class="col-md-12">
                         <div class="mt-3">
                             <label for="exampleInputMontant" class="form-label">Montant:</label>
                             <input type="number" class="form-control" id="exampleInputMontant" name="amount" value="100" aria-describedby="emailHelp">
                         </div>
                         <div class="mt-3">
                             <label for="exampleInputDevise" class="form-label">Devise:</label>
                             <select class="form-select" name="currency" aria-label="Default select example">
                                 <option selected value="XOF">XOF</option>
                                 <option value="XAF">XAF</option>
                                 <option value="CDF">CDF</option>
                                 <option value="USD">USD</option>
                             </select>
                         </div>
                    </div>
                     <div class="col-md-12 text-center mt-3">
                         <button type="submit" class="btn btn-success">Effectuer le payer</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
    </div>
</body>
</html>
