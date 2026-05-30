{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}

@php
  $nivel = (int)($user->nivel ?? 0);
  $podeEditarIdentidade = $nivel >= 10;
@endphp

<section class="mt-4">
  <div class="card border-0 shadow-sm">
    <div class="card-body">

      <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
        <div class="text-wrap-anywhere">
          <div class="fw-black mb-1">Informações do perfil</div>
          <div class="text-muted small">
            Nome e e-mail (controle institucional).
          </div>
        </div>

        @if (session('status') === 'profile-updated')
          <span class="badge text-bg-success align-self-start">
            <i class="bi bi-check2-circle me-1"></i> Dados atualizados
          </span>
        @endif

        @if (session('status') === 'profile-locked')
          <span class="badge text-bg-warning align-self-start">
            <i class="bi bi-shield-lock me-1"></i> Bloqueado
          </span>
        @endif
      </div>

      <hr class="my-3">

      @if(!$podeEditarIdentidade)
        <div class="alert alert-info d-flex align-items-start gap-2">
          <i class="bi bi-shield-lock mt-1"></i>
          <div class="small">
            <b>Alteração restrita.</b><br>
            Nome e e-mail só podem ser alterados por <b>nível 10</b>.
            Para correção, solicite via <b>Diretoria / Administração</b>.
          </div>
        </div>
      @endif

      {{-- reenviar verificação --}}
      <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
      </form>

      <form method="POST" action="{{ route('profile.update', ['tab' => 'dados']) }}">
        @csrf
        @method('PATCH')

        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label for="name" class="form-label fw-semibold">Nome completo</label>
            <input
              id="name"
              name="name"
              type="text"
              class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name', $user->name) }}"
              @disabled(!$podeEditarIdentidade)
              autocomplete="name"
              required
            >
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12 col-md-6">
            <label for="email" class="form-label fw-semibold">E-mail institucional</label>
            <input
              id="email"
              name="email"
              type="email"
              class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email', $user->email) }}"
              @disabled(!$podeEditarIdentidade)
              autocomplete="username"
              required
            >
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            {{-- status de verificação --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail)
              @if ($user->hasVerifiedEmail())
                <div class="text-success small mt-2">
                  <i class="bi bi-check-circle me-1"></i> E-mail verificado
                </div>
              @else
                <div class="alert alert-warning mt-2 mb-0 small">
                  <i class="bi bi-exclamation-triangle me-1"></i>
                  E-mail não verificado.
                  <button type="submit" form="send-verification" class="btn btn-link p-0 ms-1 align-baseline">
                    Reenviar verificação
                  </button>

                  @if (session('status') === 'verification-link-sent')
                    <div class="text-success mt-1">
                      <i class="bi bi-check-circle me-1"></i> Link enviado com sucesso.
                    </div>
                  @endif
                </div>
              @endif
            @endif
          </div>

          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mt-2">
              @if($podeEditarIdentidade)
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-save2 me-1"></i> Salvar alterações
                </button>
              @else
                <button type="button" class="btn btn-secondary" disabled>
                  <i class="bi bi-lock-fill me-1"></i> Alteração bloqueada
                </button>
              @endif

              <div class="text-muted small">
                Última atualização:
                <b>{{ optional($user->updated_at)->format('d/m/Y H:i') ?? '-' }}</b>
              </div>
            </div>
          </div>

        </div>
      </form>

    </div>
  </div>
</section>