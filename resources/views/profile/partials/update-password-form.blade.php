{{-- resources/views/profile/partials/update-password-form.blade.php --}}

@php
  $bag = $errors->getBag('updatePassword');
@endphp

<section class="mt-4">
  <div class="card border-0 shadow-sm">
    <div class="card-body">

      <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
        <div class="text-wrap-anywhere">
          <div class="fw-black mb-1">Alterar senha</div>
          <div class="text-muted small mb-0">
            Use uma senha longa e difícil de adivinhar. Evite reutilizar senhas antigas.
          </div>
        </div>

        @if (session('status') === 'password-updated')
          <span class="badge text-bg-success align-self-start">
            <i class="bi bi-check2-circle me-1"></i> Senha atualizada
          </span>
        @endif
      </div>

      <hr class="my-3">

      @if($bag && $bag->any())
        <div class="alert alert-danger">
          <div class="fw-black mb-1">Verifique os campos:</div>
          <ul class="mb-0">
            @foreach($bag->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('password.update', ['tab' => 'seguranca']) }}" class="mt-2">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label for="update_password_current_password" class="form-label fw-semibold">Senha atual</label>
            <input
              id="update_password_current_password"
              name="current_password"
              type="password"
              class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
              autocomplete="current-password"
              placeholder="Digite sua senha atual"
              required
            >
            @error('current_password', 'updatePassword')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12 col-md-6">
            <label for="update_password_password" class="form-label fw-semibold">Nova senha</label>
            <input
              id="update_password_password"
              name="password"
              type="password"
              class="form-control @error('password', 'updatePassword') is-invalid @enderror"
              autocomplete="new-password"
              placeholder="Crie uma nova senha"
              required
            >
            @error('password', 'updatePassword')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <div class="form-text">
              Dica: use letras maiúsculas, minúsculas, números e símbolos.
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label for="update_password_password_confirmation" class="form-label fw-semibold">Confirmar nova senha</label>
            <input
              id="update_password_password_confirmation"
              name="password_confirmation"
              type="password"
              class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
              autocomplete="new-password"
              placeholder="Repita a nova senha"
              required
            >
            @error('password_confirmation', 'updatePassword')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-shield-lock me-1"></i> Salvar nova senha
            </button>
          </div>
        </div>
      </form>

    </div>
  </div>
</section>