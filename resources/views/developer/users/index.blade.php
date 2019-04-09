@extends('layouts.sidebar')

@section('content')
<section class="section">
          <div class="section-header">
            <h1>Kelola Pengguna</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="#">Developer</a></div>
              <div class="breadcrumb-item">Services</div>
            </div>
          </div>

          <div class="section-body">
          
          	<div class="row">
          		<div class="col-md-12">
          			<div class="card">
		              <div class="card-header">
		                <h4><span>Kelola Pengguna</span></h4>
		              </div>
		              <div class="card-body">
		              	@if(session('success'))

                        <div class="alert alert-success" role="alert">
                            <i class="fa fa-check-circle"></i> {{ session('success') }}
                        </div>

                    	@endif
                      <div class="float-left">
                          <form method="GET">
                            <div class="input-group">
                              <input type="text" class="form-control" placeholder="Cari email atau nama" name="search">
                              <div class="input-group-append">                                            
                                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                              </div>
                            </div>
                          </form>
                        </div>
		              	<div class=" float-right">
	  		            	<a href="{{ url('developer/users/add') }}" class="btn btn-primary">Tambah</a>
	  		            </div>
                    <div class="clearfix mb-3"></div>
                          <div class="table-responsive">
                                <table class="table table-striped table-md">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Saldo</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>API Key</th>
                                    <th>Action</th>
                                </tr>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>Rp {{ number_format($user->balance) }}</td>
                                    <td>{{ $user->level }}</td>
                                    <td>
                                      <span class="badge badge-{{ ($user->status =='Active' ? 'success' : 'danger') }}">{{ $user->status }}</span>
                                    </td>
                                    <td>{{ $user->api_key }}</td>
                                    <td style="display: inline-block;">
                                        <a href="{{ url('developer/users/edit/'.$user->id)}}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                        <form method="POST" class="form-delete">
                                            @method('delete')
                                            @csrf
                                            <input type="hidden" value="{{ $user->id }}" name="id">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                                @endforeach
                            </table>
                          </div>
		              </div>
		            </div>
          		</div>
          	</div>
            
          </div>
        </section>
@endsection