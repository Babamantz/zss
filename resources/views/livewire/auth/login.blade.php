        <section>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-5"><img class="bg-img-cover bg-center"
                            src="{{ asset('assets/images/login/3.jpg') }}" alt="looginpage" /></div>
                    <div class="col-xl-7 p-0">
                        <div class="login-card">
                            <form class="theme-form login-form" wire:submit="login">
                                <h4>ZSS Login</h4>
                                <h6>Welcome back! Log in to your account.</h6>

                                <div class="form-group">
                                    <label>Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="icon-email"></i></span>
                                        <input class="form-control" type="email" wire:model="email" required />
                                    </div>
                                </div>
                                @error('email')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <div class="form-group">
                                    <label>Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="icon-lock"></i></span>
                                        <input class="form-control" type="password" wire:model="password" required />
                                        <div class="show-hide"><span class="show"></span></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="checkbox">
                                        <input id="checkbox1" type="checkbox" wire:model="remember" />
                                        <label class="text-muted" for="checkbox1">Remember password</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-primary btn-block" type="submit">
                                        Sign in
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        \
