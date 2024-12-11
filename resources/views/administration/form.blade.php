<!-- TEMPLATE APP -->
<x-template-app.app-layout>

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Sistema Integral para Recursos Humanos"
                            caption="Usuarios" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id) ? 'Modificar' : 'Agregar ' }} usuario"
                            route="{{ route('user.list') }}" />
                        <div>
                            <form action="{{ route('user.save') }}" method="POST" class="form-sample">
                                @csrf

                                <x-template-form.template-form-input-hidden name="userId"
                                    value="{{ optional($item)->id ?? '' }}" />

                                <x-template-tittle.tittle-caption-secon tittle="Datos del usuario" />

                                <div class="row">
                                    <x-template-form.template-form-input-required label="Nombre" type="text"
                                        name="userName" placeholder="Nombre"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete=""
                                        value="{{optional($item)->name ?? '' }}" />

                                    <x-template-form.template-form-input-required label="Email" type="text"
                                        name="userEmail" placeholder="Correo electrónico"
                                        value="{{ optional($item)->email ?? '' }}" autocomplete=""
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" />
                                </div>

                                @if (!isset($item->id)) <!-- Ocultar contenido en caso de modificar-->
                                    <div class="row">
                                        <x-template-form.template-form-input-required label="Password" type="password"
                                            name="userPassword" value="" placeholder="Password" autocomplete="new-password"
                                            grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                        <x-template-form.template-form-input-required label="Confirmar Password"
                                            type="password" name="userConfirmPassword" autocomplete="new-password"
                                            placeholder="Confirmar password" value=""
                                            grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                    </div>
                                @endif

                                <x-template-tittle.tittle-caption-secon tittle="Roles y estatus" />

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"
                                                style="font-size: 1rem; color: #333;">Roles</label>
                                            <div class="col-sm-9">

                                                <select style="width: 100%;"
                                                    class="js-example-basic-multiple w-100 custom-select"
                                                    name="userRoles[]" multiple="multiple">
                                                    @foreach($roleOptions as $role)
                                                                                                        <option value="{{ $role->id }}" @php
                                                                                                            $userRolesIds = !isset($item->id) ? array_column($userRoles, 'id') : $userRoles->pluck('id')->toArray();
                                                                                                        @endphp @if(in_array($role->id, old('userRoles', $userRolesIds)))
                                                                                                        selected @endif>
                                                                                                            {{ $role->name }}
                                                                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error('userRoles')
                                                    <small style="color:red; font-family: Arial, sans-serif;">
                                                        <i class="fas fa-exclamation-circle" style="color:red;"></i>
                                                        {{ $message }}
                                                    </small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <x-template-tittle.tittle-caption-secon tittle="Asociacion con Nómina" />


                                <div class="container">
                                    <div class="row">
                                        <x-template-form.template-form-check name="userEsPorNomina"
                                            value="{{ !isset($item->es_por_nomina) ? 'false' : $item->es_por_nomina }}"
                                            text="Vincular con nómina" />
                                    </div>
                                </div>




                                <div id="is_nomina">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label"
                                                    style="font-size: 1rem; color: #333;">Tipo de nómina</label>
                                                <div class="col-sm-9">
                                                    <select class=" selectpicker dropup show-tick" data-width="auto"
                                                        data-size="5" data-live-search="true" data-dropup-auto="false">
                                                        <option data-tokens="ketchup mustard">Hot Dog, Fries and a Soda
                                                        </option>
                                                        <option data-tokens="mustard">Burger, Shake and a Smile</option>
                                                        <option data-tokens="frosting">Sugar, Spice and all things nice
                                                        </option>
                                                        <option data-tokens="frosting">Sugar, Spice and all things nice
                                                        </option>
                                                        <option data-tokens="frosting">Sugar, Spice and all things nice
                                                        </option>
                                                        <option data-tokens="frosting">Sugar, Spice and all things nice
                                                        </option>
                                                        <option data-tokens="frosting">Sugar, Spice and all things nice
                                                        </option>
                                                        <option data-tokens="frosting">Sugar, Spice and all things nice
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <x-template-form.template-form-input-required label="CURP" type="text"
                                            name="curp" placeholder="CURP" autocomplete=""
                                            grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4"
                                            value="{{ optional($item)->name ?? '' }}" />
                                    </div>
                                </div>


                                <x-template-button.button-form-footer routeBack="{{ route('user.list') }}" />

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-template-app.app-layout>

<!-- CODE SCRIPT-->
<script src="{{ asset('assets/js/app/administration/user/form.js') }}"></script>