{{-- resources/views/profile/partials/delete-user-form.blade.php --}}

@php
  $nivel = (int) (auth()->user()->nivel ?? 0);
  $bag = $errors->getBag('userDeletion');
  $hasBagError = $bag && $bag->any();
@endphp

<section class="mt-4">

  {{-- ✅ SEM PERMISSÃO --}}
  @if($nivel < 10)
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-start gap-3">
          <div class="gov-emblem" style="width:44px;height:44px;border-radius:14px;">
            <div class="gov-emblem-dot" style="width:14px;height:14px;"></div>
          </div>

          <div class="text-wrap-anywhere">
            <div class="fw-black mb-1">Excluir conta</div>
            <div class="text-muted small">
              Sua conta não possui permissão para exclusão. Caso precise, solicite ao setor responsável (Administração / RH).
            </div>
          </div>
        </div>
      </div>
    </div>

  @else
    {{-- ✅ COM PERMISSÃO (NÍVEL 10+) --}}
    <div class="card border-0 shadow-sm">
      <div class="card-body">

        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
          <div class="text-wrap-anywhere">
            <div class="text-danger fw-black mb-1">Excluir conta</div>
            <div class="text-muted small mb-0">
              Esta ação é <b>irreversível</b>. Use apenas em casos autorizados.
            </div>
          </div>

          <div>
            <button type="button"
              class="btn btn-outline-danger"
              data-bs-toggle="modal"
              data-bs-target="#modalDeleteAccount">
              <i class="bi bi-trash3 me-1"></i> Excluir conta
            </button>
          </div>
        </div>

        {{-- Mensagem de erro de senha --}}
        @if($hasBagError)
          <div class="alert alert-danger mt-3 mb-0">
            <div class="fw-black mb-1">Não foi possível excluir a conta</div>
            <div class="small">
              {{ $bag->first('password') ?? 'Verifique os dados e tente novamente.' }}
            </div>
          </div>
        @endif

      </div>
    </div>

    {{-- ✅ MODAL Bootstrap --}}
    <div class="modal fade" id="modalDeleteAccount" tabindex="-1" aria-labelledby="modalDeleteAccountLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title fw-black text-danger" id="modalDeleteAccountLabel">Confirmar exclusão</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>

          <form method="POST" action="{{ route('profile.destroy', ['tab' => 'seguranca']) }}">
            @csrf
            @method('DELETE')

            <div class="modal-body">

              <div class="alert alert-warning mb-3">
                <div class="fw-black mb-1">Atenção</div>
                <div class="small">
                  Ao confirmar, a conta será excluída <b>definitivamente</b>.
                  Digite sua senha para confirmar.
                </div>
              </div>

              <div class="mb-2">
                <label for="delete_password" class="form-label fw-semibold">Senha</label>
                <input
                  id="delete_password"
                  name="password"
                  type="password"
                  class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                  placeholder="Digite sua senha para confirmar"
                  autocomplete="current-password"
                  required
                />

                @error('password', 'userDeletion')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-danger">
                <i class="bi bi-exclamation-triangle me-1"></i> Excluir permanentemente
              </button>
            </div>

          </form>

        </div>
      </div>
    </div>

    {{-- ✅ Se voltou com erro no bag userDeletion, reabrir modal automaticamente --}}
    @if($hasBagError)
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          const el = document.getElementById('modalDeleteAccount');
          if (el && window.bootstrap) {
            const modal = new bootstrap.Modal(el);
            modal.show();

            // foco no campo senha
            setTimeout(() => {
              const inp = document.getElementById('delete_password');
              if (inp) inp.focus();
            }, 250);
          }
        });
      </script>
    @endif

  @endif
</section>