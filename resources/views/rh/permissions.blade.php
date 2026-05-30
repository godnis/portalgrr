@extends('layouts.app')

@section('content')
@php
  // $users (paginated) e $perms (keyBy user_id) vêm do controller
  $pageTotal = method_exists($users, 'count') ? $users->count() : 0;

  // ✅ agora só 2 permissões (can_hierarquia + can_controle_saida)
  $anyEnabledCount = 0;
  foreach(($users ?? []) as $u){
    $p = $perms[$u->id] ?? null;

    $any = (bool)(
      ($p->can_hierarquia ?? false) ||
      ($p->can_controle_saida ?? false)
    );

    if ($any) $anyEnabledCount++;
  }

  $badge = function(bool $on){
    return $on
      ? '<span class="pill pill--on">Liberado</span>'
      : '<span class="pill pill--off">Bloqueado</span>';
  };
@endphp

<div class="rhP-wrap">
  {{-- HERO --}}
  <div class="rhP-hero">
    <div class="rhP-hero__bg"></div>

    <div class="rhP-hero__inner">
      <div class="rhP-hero__left">
        <div class="rhP-kicker">RH • PERMISSÕES</div>
        <h1 class="rhP-title">Gerenciar Permissões</h1>
        <div class="rhP-sub">
          Libere acesso de <b>edição</b> por usuário (módulos do RH). Visualização continua liberada.
        </div>

        <div class="rhP-chips">
          <span class="rhP-chip">Usuários nesta página: <b>{{ $pageTotal }}</b></span>
          <span class="rhP-chip">Com algum acesso liberado: <b>{{ $anyEnabledCount }}</b></span>
          <span class="rhP-chip rhP-chip--soft">Somente nível 9+ vê esta tela</span>
        </div>
      </div>

      <div class="rhP-hero__right">
        <a href="{{ route('rh.index') }}" class="btn btn-outline-secondary rhP-btn">Voltar</a>
      </div>
    </div>
  </div>

  {{-- SEARCH --}}
  <div class="rhP-card">
    <div class="rhP-card__body">
      <form class="row g-2 align-items-center" method="GET" action="{{ route('rh.permissions') }}">
        <div class="col-lg-8">
          <input
            class="form-control rhP-input"
            name="q"
            value="{{ $q ?? '' }}"
            placeholder="Buscar por Nome / RG / Passaporte(ID)..."
          >
        </div>
        <div class="col-lg-4 d-flex gap-2">
          <button class="btn btn-primary rhP-btn rhP-btn--primary w-100" type="submit">Buscar</button>
          <a class="btn btn-outline-secondary rhP-btn w-100" href="{{ route('rh.permissions') }}">Limpar</a>
        </div>
      </form>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success rhP-alert">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger rhP-alert">
      <div class="fw-semibold mb-1">Corrija os campos:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- LIST --}}
  <div class="rhP-list">
    @forelse($users as $user)
      @php
        $p = $perms[$user->id] ?? null;

        // ✅ somente 2 campos
        $can_hierarquia     = (bool) ($p->can_hierarquia ?? false);
        $can_controle_saida = (bool) ($p->can_controle_saida ?? false);

        $nome = (string) ($user->name ?? '—');
        $parts = preg_split('/\s+/', trim($nome));
        $ini = '';
        if (!empty($parts[0])) $ini .= mb_strtoupper(mb_substr($parts[0], 0, 1));
        if (!empty($parts[1])) $ini .= mb_strtoupper(mb_substr($parts[1], 0, 1));
        if ($ini === '') $ini = 'PRF';
      @endphp

      <div class="rhP-userCard" data-user-card>
        <div class="rhP-userCard__left">
          <div class="rhP-avatar">{{ $ini }}</div>
          <div class="rhP-userMeta">
            <div class="rhP-userName">{{ $user->name }}</div>
            <div class="rhP-userSub">
              <span><b>ID:</b> {{ $user->id }}</span>
              <span class="dot"></span>
              <span><b>Nível:</b> {{ (int)($user->nivel ?? 0) }}</span>
              @if(!empty($user->rg))
                <span class="dot"></span>
                <span><b>RG:</b> {{ $user->rg }}</span>
              @endif
            </div>

            <div class="rhP-badges">
              {!! $badge($can_hierarquia) !!}<span class="rhP-badgeLabel">Hierarquia</span>
              {!! $badge($can_controle_saida) !!}<span class="rhP-badgeLabel">Controle de Saída</span>
            </div>
          </div>
        </div>

        <div class="rhP-userCard__right">
          <form
            method="POST"
            action="{{ route('rh.permissions.update', $user) }}"
            class="rhP-form"
            data-perm-form
          >
            @csrf
            @method('PUT')

            {{-- ✅ hidden = 0 para checkbox não “sumir” --}}
            <input type="hidden" name="can_hierarquia" value="0">
            <input type="hidden" name="can_controle_saida" value="0">

            <div class="rhP-switchGrid">
              <label class="rhP-switch">
                <span class="rhP-switch__label">Hierarquia</span>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input jsPerm" type="checkbox" name="can_hierarquia" value="1"
                    @checked($can_hierarquia)>
                </div>
              </label>

              <label class="rhP-switch">
                <span class="rhP-switch__label">Controle de Saída</span>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input jsPerm" type="checkbox" name="can_controle_saida" value="1"
                    @checked($can_controle_saida)>
                </div>
              </label>
            </div>

            <div class="rhP-actions">
              <button type="button" class="btn btn-outline-secondary rhP-btn jsBlockAll">
                Bloquear tudo
              </button>
              <button type="button" class="btn btn-outline-primary rhP-btn jsAllowAll">
                Liberar tudo
              </button>
              <button type="submit" class="btn btn-primary rhP-btn rhP-btn--primary jsSave" disabled>
                Salvar
              </button>
            </div>

            <div class="rhP-hint">
              Dica: “Salvar” só ativa quando você altera alguma permissão.
            </div>
          </form>
        </div>
      </div>
    @empty
      <div class="rhP-empty">
        Nenhum usuário encontrado.
      </div>
    @endforelse
  </div>

  {{-- PAGINAÇÃO --}}
  @if(method_exists($users, 'links'))
    <div class="rhP-pagination">
      {{ $users->links() }}
    </div>
  @endif
</div>

<style>
  .rhP-wrap{ max-width: 1320px; margin: 0 auto; padding: 18px 18px 44px; }

  /* HERO */
  .rhP-hero{
    position: relative;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid rgba(2,6,23,.08);
    background:#fff;
    box-shadow: 0 18px 44px rgba(2,6,23,.08);
    margin-bottom: 14px;
  }
  .rhP-hero__bg{
    position:absolute; inset:0;
    background:
      radial-gradient(1100px 260px at 10% 0%, rgba(59,130,246,.18), transparent 60%),
      radial-gradient(900px 260px at 90% 0%, rgba(16,185,129,.14), transparent 60%),
      linear-gradient(180deg, rgba(2,6,23,.03), transparent 35%);
    pointer-events:none;
  }
  .rhP-hero__inner{
    position:relative;
    display:flex; justify-content:space-between; gap:16px;
    padding: 20px;
    flex-wrap: wrap;
  }
  .rhP-kicker{ font-size: 12px; font-weight: 900; letter-spacing:.12em; color: rgba(2,6,23,.55); }
  .rhP-title{ font-size: 28px; font-weight: 950; margin: 4px 0 3px; color: #0b1220; letter-spacing:-.02em; }
  .rhP-sub{ color: rgba(2,6,23,.62); font-size: 14px; }
  .rhP-chips{ display:flex; gap:10px; flex-wrap:wrap; margin-top: 10px; }
  .rhP-chip{
    display:inline-flex; align-items:center;
    padding: 7px 10px;
    border-radius: 999px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(248,250,252,.9);
    font-size: 13px;
    color: rgba(2,6,23,.75);
    font-weight: 700;
  }
  .rhP-chip--soft{ background: rgba(59,130,246,.08); border-color: rgba(59,130,246,.18); color: rgba(37,99,235,.95); }

  .rhP-btn{ border-radius: 12px; padding: 10px 14px; font-weight: 800; }
  .rhP-btn--primary{ box-shadow: 0 10px 22px rgba(37,99,235,.18); }

  /* CARD */
  .rhP-card{
    border-radius: 16px;
    border: 1px solid rgba(2,6,23,.08);
    background:#fff;
    box-shadow: 0 12px 28px rgba(2,6,23,.05);
    overflow:hidden;
    margin-bottom: 12px;
  }
  .rhP-card__body{ padding: 14px; }
  .rhP-input{ border-radius: 14px; padding: 12px 14px; }

  .rhP-alert{ border-radius: 14px; }

  /* LIST / USER CARD */
  .rhP-list{ display:flex; flex-direction:column; gap: 12px; margin-top: 10px; }
  .rhP-userCard{
    display:flex; gap: 16px; flex-wrap: wrap;
    padding: 16px;
    border-radius: 18px;
    border: 1px solid rgba(2,6,23,.08);
    background:#fff;
    box-shadow: 0 12px 30px rgba(2,6,23,.06);
  }
  .rhP-userCard__left{ display:flex; gap: 12px; min-width: 320px; flex: 1 1 380px; }
  .rhP-userCard__right{ flex: 1 1 520px; }

  .rhP-avatar{
    width: 44px; height: 44px; border-radius: 14px;
    display:flex; align-items:center; justify-content:center;
    font-weight: 950; color:#0b1220;
    background:
      radial-gradient(24px 24px at 30% 25%, rgba(59,130,246,.25), transparent 60%),
      radial-gradient(24px 24px at 70% 75%, rgba(16,185,129,.18), transparent 60%),
      #eef2ff;
    border: 1px solid rgba(2,6,23,.10);
    flex: 0 0 auto;
  }
  .rhP-userName{ font-weight: 950; color:#0b1220; line-height: 1.1; font-size: 16px; }
  .rhP-userSub{ font-size: 12px; color: rgba(2,6,23,.60); margin-top: 4px; display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
  .dot{ width:4px; height:4px; border-radius:99px; background: rgba(2,6,23,.35); display:inline-block; }

  .rhP-badges{ display:flex; flex-wrap:wrap; gap:8px 10px; margin-top: 10px; align-items:center; }
  .rhP-badgeLabel{ font-size: 12px; color: rgba(2,6,23,.65); margin-right: 8px; }

  .pill{
    display:inline-flex; align-items:center; justify-content:center;
    padding: 4px 10px;
    border-radius: 999px;
    font-weight: 900;
    font-size: 11px;
    border: 1px solid rgba(2,6,23,.10);
    background:#fff;
    color: rgba(2,6,23,.70);
  }
  .pill--on{ background: rgba(16,185,129,.12); border-color: rgba(16,185,129,.24); color:#0f766e; }
  .pill--off{ background: rgba(148,163,184,.12); border-color: rgba(148,163,184,.26); color: rgba(2,6,23,.62); }

  /* SWITCH GRID */
  .rhP-switchGrid{
    display:grid;
    grid-template-columns: repeat(2, minmax(220px, 1fr));
    gap: 10px;
  }
  @media (max-width: 992px){
    .rhP-switchGrid{ grid-template-columns: 1fr; }
  }
  .rhP-switch{
    display:flex; align-items:center; justify-content:space-between;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(248,250,252,.9);
    gap: 10px;
  }
  .rhP-switch__label{ font-weight: 900; color: rgba(2,6,23,.82); font-size: 13px; }

  .form-check-input{ cursor:pointer; }
  .form-switch .form-check-input{ width: 44px; height: 22px; }

  .rhP-actions{
    display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;
    margin-top: 12px;
  }
  .rhP-hint{ margin-top: 8px; font-size: 12px; color: rgba(2,6,23,.55); }

  .rhP-empty{
    border-radius: 18px;
    border: 1px dashed rgba(2,6,23,.18);
    background: rgba(248,250,252,.9);
    padding: 20px;
    text-align:center;
    color: rgba(2,6,23,.65);
    font-weight: 700;
  }

  .rhP-pagination{ margin-top: 14px; }
</style>

<script>
  (function(){
    const cards = document.querySelectorAll('[data-user-card]');

    cards.forEach(card => {
      const form = card.querySelector('[data-perm-form]');
      if (!form) return;

      const checks = Array.from(form.querySelectorAll('.jsPerm'));
      const btnSave = form.querySelector('.jsSave');
      const btnAllowAll = form.querySelector('.jsAllowAll');
      const btnBlockAll = form.querySelector('.jsBlockAll');

      const snapshot = () => checks.map(c => c.checked ? '1' : '0').join('');
      let base = snapshot();

      const refresh = () => {
        const now = snapshot();
        const changed = now !== base;
        if (btnSave) btnSave.disabled = !changed;
      };

      checks.forEach(c => c.addEventListener('change', refresh));

      if (btnAllowAll) {
        btnAllowAll.addEventListener('click', () => {
          checks.forEach(c => c.checked = true);
          refresh();
        });
      }

      if (btnBlockAll) {
        btnBlockAll.addEventListener('click', () => {
          checks.forEach(c => c.checked = false);
          refresh();
        });
      }

      form.addEventListener('submit', () => {
        if (btnSave) btnSave.disabled = true;
      });

      refresh();
    });
  })();
</script>
@endsection
