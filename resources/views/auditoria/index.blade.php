@extends('layouts.app')

@section('content')
@php
  $unlocked = $unlocked ?? false;
  $openAfter = $openAfter ?? null;
  $unlockedUntilTs = $unlockedUntilTs ?? null;

  // vindo do controller
  $groups = is_array($groups ?? null) ? $groups : [];
  $acaoLabel = is_array($acaoLabel ?? null) ? $acaoLabel : [];
  $entidades = is_array($entidades ?? null) ? $entidades : [];

  // fallback de labels (caso algo não venha no controller/config)
  $acaoLabelDefault = [
    // Auth
    'login'           => 'Login realizado',
    'logout'          => 'Logout realizado',
    'login_falhou'    => 'Login falhou (senha/credencial)',
    'login_bloqueado' => 'Login bloqueado (sem permissão/status)',

    // Relatórios
    'relatorio_criado'               => 'Relatório criado',
    'relatorio_editado'              => 'Relatório editado',
    'relatorio_finalizado'           => 'Relatório finalizado',
    'relatorio_criado_e_finalizado'  => 'Relatório criado e finalizado',
    'relatorio_aprovado'             => 'Relatório aprovado',
    'relatorio_reprovado'            => 'Relatório reprovado',
    'relatorio_decisao_aberta'       => 'Relatórios: decisão aberta (editar)',

    // Efetivo (inclui massa/negações)
    'efetivo_index_aberto'             => 'Efetivo: abriu a lista',
    'efetivo_show_aberto'              => 'Efetivo: abriu ficha do oficial',
    'efetivo_create_aberto'            => 'Efetivo: abriu cadastro',
    'efetivo_create_negado'            => 'Efetivo: cadastro negado',
    'efetivo_criado'                   => 'Efetivo: oficial cadastrado',
    'efetivo_edit_aberto'              => 'Efetivo: abriu edição',
    'efetivo_update_negado'            => 'Efetivo: edição negada',
    'efetivo_editado'                  => 'Efetivo: oficial editado',
    'efetivo_promocao_negada'          => 'Efetivo: promoção negada',
    'efetivo_promovido'                => 'Efetivo: oficial promovido',
    'efetivo_promocao_massa_negada'    => 'Efetivo: promoção em massa negada',
    'efetivo_promovido_massa'          => 'Efetivo: oficial promovido (massa)',
    'efetivo_promocao_massa_executada' => 'Efetivo: promoção em massa executada',
    'efetivo_suspender_negado'         => 'Efetivo: suspender negado',
    'efetivo_suspenso'                 => 'Efetivo: oficial suspenso',
    'efetivo_reativar_negado'          => 'Efetivo: reativar negado',
    'efetivo_reativado'                => 'Efetivo: oficial reativado',
    'efetivo_destroy_negado'           => 'Efetivo: remoção negada',
    'efetivo_removido'                 => 'Efetivo: oficial removido',

    // Auditoria
    'auditoria_unlock_sucesso' => 'Auditoria: desbloqueio autorizado',
    'auditoria_unlock_falha'   => 'Auditoria: tentativa de desbloqueio (falhou)',
    'auditoria_lock'           => 'Auditoria: travada',

    // Atendimento
    'atendimento_publico_enviado' => 'Canais de Atendimento: mensagem enviada (público)',
    'atendimento_index_aberto'    => 'Canais de Atendimento: painel aberto',
    'atendimento_show_aberto'     => 'Canais de Atendimento: atendimento visualizado',
    'atendimento_status_alterado' => 'Canais de Atendimento: status alterado',

    // Pré-inscrições
    'preinscricao_admin_index_aberto'    => 'Pré-inscrições: lista aberta (admin)',
    'preinscricao_admin_show_aberto'     => 'Pré-inscrições: ficha aberta (admin)',
    'preinscricao_admin_status_alterado' => 'Pré-inscrições: decisão registrada (admin)',

    // Tickets (usuário)
    'ticket_user_index_aberto'  => 'Tickets: meus tickets abertos',
    'ticket_user_create_aberto' => 'Tickets: tela abrir ticket',
    'ticket_user_criado'        => 'Tickets: ticket criado',
    'ticket_show_aberto'        => 'Tickets: ticket visualizado',
    'ticket_user_respondeu'     => 'Tickets: usuário respondeu',

    // Tickets (admin)
    'ticket_admin_index_aberto'    => 'Tickets (Admin): lista aberta',
    'ticket_admin_show_aberto'     => 'Tickets (Admin): ticket visualizado',
    'ticket_admin_status_alterado' => 'Tickets (Admin): status alterado',
    'ticket_admin_respondeu'       => 'Tickets (Admin): resposta enviada',
  ];

  // ✅ garante fallback
  $acaoLabel = array_merge($acaoLabelDefault, $acaoLabel);

  // ✅ normaliza groups (garante label/items)
  foreach ($groups as $gid => $g) {
    if (!is_array($g)) { unset($groups[$gid]); continue; }
    $groups[$gid]['label'] = (string) ($g['label'] ?? $gid);
    $groups[$gid]['items'] = is_array($g['items'] ?? null) ? $g['items'] : [];
  }

  $selectedAcao = (string) request('acao', '');

  // ✅ descobre o grupo da ação selecionada (se houver)
  $selectedGroup = '';
  if ($selectedAcao !== '') {
    foreach ($groups as $gid => $g) {
      if (array_key_exists($selectedAcao, $g['items'] ?? [])) { $selectedGroup = (string) $gid; break; }
    }
  }
@endphp

<div class="audit-wrap container-fluid py-2" style="max-width: 1300px;">

  {{-- HERO / HEADER --}}
  <div class="audit-hero mb-3">
    <div class="audit-hero__bg"></div>

    <div class="audit-hero__content d-flex align-items-start justify-content-between flex-wrap gap-2">
      <div>
        <div class="audit-kicker">GRR • PRF</div>
        <h4 class="audit-title mb-1 fw-black">Auditoria do Sistema</h4>
        <div class="audit-sub fw-semibold">
          Registro oficial de ações • evidência • rastreabilidade.
        </div>

        <div class="audit-badges mt-2 d-flex gap-2 flex-wrap align-items-center">
          <span class="audit-pill">AUDITORIA ATIVA</span>
          <span class="audit-pill {{ $unlocked ? 'is-ok' : 'is-muted' }}">
            {{ $unlocked ? 'DESBLOQUEADO' : 'PROTEGIDO' }}
          </span>

          @if($unlocked)
            <form method="POST" action="{{ route('auditoria.travar') }}" class="m-0">
              @csrf
              <button class="btn btn-sm btn-outline-danger audit-btn-soft">
                Travar
              </button>
            </form>
          @else
            <button class="btn btn-sm btn-primary audit-btn-soft"
                    type="button" data-bs-toggle="modal" data-bs-target="#auditUnlockModal">
              Desbloquear
            </button>
          @endif
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        {{-- espaço para ação futura --}}
      </div>
    </div>
  </div>

  {{-- FLASH --}}
  @if(session('success'))
    <div class="alert alert-success rounded-4">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
  @endif

  {{-- FILTROS --}}
  <div class="audit-card mb-3">
    <div class="audit-card__body p-3 p-md-4">

      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <div class="fw-black audit-card__title">Filtros de Consulta</div>
          <div class="audit-muted small fw-semibold">
            Filtre por ação (em grupos), RG (ator/alvo), entidade e período.
          </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('auditoria.index') }}" class="btn btn-sm btn-outline-secondary audit-btn-soft">
            Limpar
          </a>
          <button form="auditoriaFilter" class="btn btn-sm btn-primary audit-btn-soft">
            Filtrar
          </button>
        </div>
      </div>

      <form id="auditoriaFilter" method="GET" class="row g-2 g-md-3">

        {{-- Grupo --}}
        <div class="col-md-2">
          <label class="small audit-muted fw-bold audit-top-label">Ação (grupo)</label>
          <select id="acaoGroup" class="form-select form-select-sm audit-input">
            <option value="" @selected($selectedGroup==='')>Todas</option>
            @foreach($groups as $gid => $g)
              <option value="{{ $gid }}" @selected($selectedGroup === (string)$gid)>{{ $g['label'] ?? $gid }}</option>
            @endforeach
          </select>
        </div>

        {{-- Ação específica --}}
        <div class="col-md-4">
          <label class="small audit-muted fw-bold audit-top-label">Ação (específica)</label>
          <select name="acao" id="acaoSelect" class="form-select form-select-sm audit-input">
            <option value="">Todas</option>

            @foreach($groups as $gid => $g)
              <optgroup label="{{ $g['label'] ?? $gid }}" data-group="{{ $gid }}">
                @foreach(($g['items'] ?? []) as $code => $lbl)
                  @php
                    $code = (string) $code;
                    $lbl  = (string) ($acaoLabel[$code] ?? $lbl ?? $code);
                  @endphp
                  <option value="{{ $code }}" @selected($selectedAcao === $code)>{{ $lbl }}</option>
                @endforeach
              </optgroup>
            @endforeach

          </select>
          <div class="audit-muted small mt-1">Dica: selecione um grupo primeiro para reduzir a lista.</div>
        </div>

        <div class="col-md-2">
          <label class="small audit-muted fw-bold audit-top-label">RG (ator/alvo)</label>
          <input name="rg" class="form-control form-control-sm audit-input"
                 value="{{ request('rg') }}" placeholder="Ex.: 12178">
        </div>

        <div class="col-md-2">
          <label class="small audit-muted fw-bold audit-top-label">Entidade</label>
          <select name="entidade_tipo" class="form-select form-select-sm audit-input">
            <option value="">Todas</option>
            @foreach($entidades as $t)
              <option value="{{ $t }}" @selected(request('entidade_tipo') === $t)>{{ $t }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-1">
          <label class="small audit-muted fw-bold audit-top-label">Início</label>
          <input type="date" name="data_inicio" class="form-control form-control-sm audit-input"
                 value="{{ request('data_inicio') }}">
        </div>

        <div class="col-md-1">
          <label class="small audit-muted fw-bold audit-top-label">Fim</label>
          <input type="date" name="data_fim" class="form-control form-control-sm audit-input"
                 value="{{ request('data_fim') }}">
        </div>

        <div class="col-md-1">
          <label class="small audit-muted fw-bold audit-top-label">Por pág.</label>
          @php $pp = (int)request('per_page', 25); @endphp
          <select name="per_page" class="form-select form-select-sm audit-input">
            @foreach([10,25,30,50,100] as $n)
              <option value="{{ $n }}" @selected($pp===$n)>{{ $n }}</option>
            @endforeach
          </select>
        </div>

      </form>

    </div>
  </div>

  {{-- LISTA --}}
  <div class="audit-card">
    <div class="audit-card__body p-0">

      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0 audit-table">
          <thead>
            <tr>
              <th style="width: 170px;">Data/Hora</th>
              <th style="width: 260px;">Ação</th>
              <th style="width: 260px;">Servidor</th>
              <th style="width: 120px;">RG</th>
              <th style="width: 150px;">Entidade</th>
              <th style="width: 90px;">ID</th>
              <th style="width: 220px;">Rota</th>
              <th style="width: 160px;">IP</th>
              <th style="width: 220px;" class="text-end">Ações</th>
            </tr>
          </thead>

          <tbody>
            @forelse($auditorias as $log)
              @php
                $det = $log->detalhes ?? null;

                $detStr = '';
                if (is_array($det) || is_object($det)) {
                  $detStr = json_encode($det, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                } else {
                  $detStr = $det ? (string)$det : '';
                }

                $collapseId = 'audlog_'.$log->id;

                $rgLinha = $log->actor_rg
                  ?? data_get($log->detalhes, 'actor_rg')
                  ?? $log->alvo_rg
                  ?? data_get($log->detalhes, 'alvo_rg')
                  ?? ($log->user?->rg ?? '—');

                $idLinha = $log->alvo_user_id
                  ?? data_get($log->detalhes, 'alvo_user_id')
                  ?? ($log->user_id ?? '—');

                $entLinha = $log->entidade_tipo ?: (data_get($log->detalhes, 'alvo_user_id') ? 'User' : '—');

                $routeName = $log->route_name ?? data_get($log->detalhes, 'route') ?? '—';
                $method    = $log->method ?? data_get($log->detalhes, 'method');
                $routeLine = trim(($method ? $method.' ' : '').$routeName);

                $acaoCode = (string) ($log->acao ?? '');
                $acaoNice = $acaoLabel[$acaoCode] ?? ($acaoCode !== '' ? ucwords(str_replace('_',' ',$acaoCode)) : '—');
              @endphp

              <tr>
                <td class="fw-semibold">
                  {{ optional($log->created_at)->format('d/m/Y H:i:s') ?? '—' }}
                  @if(!empty($log->request_id))
                    <div class="audit-muted small font-monospace mt-1" title="request_id (correlação)">
                      {{ $log->request_id }}
                    </div>
                  @endif
                </td>

                <td class="fw-black">
                  <span class="audit-badge">
                    {{ $acaoNice }}
                  </span>
                  <div class="audit-muted small mt-1 font-monospace" style="line-height:1.2;">
                    {{ $acaoCode ?: '—' }}
                  </div>
                </td>

                <td>
                  <div class="fw-semibold">
                    {{ $log->actor_nome ?? $log->user?->name ?? data_get($log->detalhes,'actor_nome') ?? '—' }}
                  </div>
                  <div class="audit-muted small">
                    ID: {{ $log->user_id ?? data_get($log->detalhes,'actor_user_id') ?? '—' }}
                    @if(!empty($log->user?->cargo ?? null))
                      <span class="mx-2">•</span>{{ $log->user?->cargo }}
                    @endif
                  </div>
                </td>

                <td class="fw-semibold">{{ $rgLinha }}</td>
                <td class="fw-semibold">{{ $entLinha }}</td>
                <td class="fw-semibold">{{ $idLinha }}</td>

                <td class="fw-semibold font-monospace" style="font-size:12px;">
                  {{ $routeLine }}
                </td>

                <td class="fw-semibold font-monospace">
                  @if($unlocked)
                    {{ $log->ip ?? '—' }}
                  @else
                    <span class="badge text-bg-secondary">Protegido</span>
                  @endif
                </td>

                <td class="text-end">
                  @if(!empty($detStr))
                    @if($unlocked)
                      <button class="btn btn-sm btn-primary audit-btn-soft"
                              type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                        Detalhes
                      </button>
                    @else
                      <button class="btn btn-sm btn-primary audit-btn-soft audit-need-pass"
                              type="button" data-target="#{{ $collapseId }}" data-log-id="{{ $log->id }}">
                        Detalhes
                      </button>
                    @endif
                  @else
                    <span class="audit-muted small">—</span>
                  @endif
                </td>
              </tr>

              <tr>
                <td colspan="9" class="p-0">
                  @if($unlocked && !empty($detStr))
                    <div class="collapse" id="{{ $collapseId }}">
                      <div class="audit-details p-3 p-md-4">
                        <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                          <div>
                            <div class="fw-black mb-1">Detalhes (evidência)</div>
                            <div class="audit-muted small fw-semibold">JSON/Texto • manter para rastreabilidade.</div>
                            @if(!empty($log->url))
                              <div class="audit-muted small mt-1"><b>URL:</b> {{ $log->url }}</div>
                            @endif
                            @if(!empty($log->user_agent))
                              <div class="audit-muted small mt-1"><b>User-Agent:</b> {{ $log->user_agent }}</div>
                            @endif
                          </div>

                          <button class="btn btn-sm btn-outline-secondary audit-btn-soft"
                                  type="button" data-copy="#pre_{{ $collapseId }}">
                            Copiar
                          </button>
                        </div>

                        <pre id="pre_{{ $collapseId }}" class="audit-pre mt-2 mb-0">{{ $detStr }}</pre>
                      </div>
                    </div>
                  @endif
                </td>
              </tr>

            @empty
              <tr>
                <td colspan="9" class="text-center audit-muted py-4">Sem registros.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-3 px-md-4 py-3 audit-pagination">
        {{ $auditorias->appends(request()->query())->links('pagination::bootstrap-5') }}
      </div>

    </div>
  </div>

</div>

{{-- MODAL: DESBLOQUEAR --}}
<div class="modal fade" id="auditUnlockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:18px;">
      <div class="modal-header">
        <h5 class="modal-title fw-black">Desbloquear Auditoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <form method="POST" action="{{ route('auditoria.revelar') }}" id="auditUnlockForm">
        @csrf

        <div class="modal-body">
          <div class="text-muted fw-semibold mb-3">
            Para ver <b>IP</b> e <b>Detalhes</b>, informe a senha de auditoria.
            Todas as tentativas são registradas.
          </div>

          <label class="form-label fw-bold">Senha</label>
          <input type="password" name="senha" class="form-control"
                 style="border-radius:12px;font-weight:800;"
                 placeholder="Digite a senha" required autocomplete="current-password">

          <input type="hidden" name="open_after" id="auditOpenAfter" value="">
          <input type="hidden" name="log_id" id="auditLogId" value="">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;font-weight:900;">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="border-radius:12px;font-weight:900;">
            Desbloquear
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  /* ==========================================================
     ✅ LIGHT (padrão) — fica branco como estava
     ========================================================== */
  .audit-wrap{
    --audit-surface: #ffffff;
    --audit-surface-2: #ffffff;
    --audit-border: rgba(15,23,42,.10);
    --audit-border-2: rgba(15,23,42,.08);
    --audit-text: rgba(15,23,42,.95);
    --audit-muted: rgba(15,23,42,.62);
    --audit-head: rgba(15,23,42,.70);
    --audit-shadow: 0 10px 30px rgba(2,6,23,.06);
    --audit-hero-shadow: 0 18px 55px rgba(2,6,23,.10);
    --audit-radius: 18px;
  }

  .audit-top-label{ display:block; line-height:1.05; margin-bottom:.35rem; }
  .audit-muted{ color: var(--audit-muted) !important; }

  .audit-hero{
    position: relative;
    border-radius: var(--audit-radius);
    overflow: hidden;
    background: var(--audit-surface);
    border: 1px solid var(--audit-border);
    box-shadow: var(--audit-hero-shadow);
  }
  .audit-hero__bg{ display:none; } /* Light: sem “dark glass” */
  .audit-hero__content{ position:relative; padding: 16px 16px; }
  .audit-kicker{
    color: var(--audit-muted);
    font-weight:900; letter-spacing:.16em; font-size:11px; text-transform:uppercase;
  }
  .audit-title{ color: var(--audit-text); }
  .audit-sub{ color: var(--audit-muted); }

  .audit-pill{
    display:inline-flex; align-items:center;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid var(--audit-border);
    background: rgba(15,23,42,.03);
    color: var(--audit-head);
    font-weight: 950;
    font-size: 12px;
  }
  .audit-pill.is-ok{ background: rgba(16,185,129,.10); border-color: rgba(16,185,129,.25); color: rgba(15,23,42,.85); }
  .audit-pill.is-muted{ background: rgba(15,23,42,.03); }

  .audit-btn-soft{ border-radius:12px; font-weight:900; }

  .audit-card{
    border-radius: var(--audit-radius);
    overflow:hidden;
    background: var(--audit-surface-2);
    border: 1px solid var(--audit-border);
    box-shadow: var(--audit-shadow);
  }
  .audit-card__body{ background: transparent; }
  .audit-card__title{ color: var(--audit-text); }

  .audit-input{
    border-radius:12px !important;
  }

  .audit-table{
    color: var(--audit-text);
  }
  .audit-table thead th{
    font-size: 11px;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--audit-muted);
    background: rgba(15,23,42,.03);
    border-bottom: 1px solid var(--audit-border) !important;
    padding: 12px 14px;
    white-space: nowrap;
  }
  .audit-table tbody td{
    border-top: 1px solid var(--audit-border-2) !important;
    padding: 12px 14px;
    vertical-align: middle;
  }
  .audit-table tbody tr:hover td{
    background: rgba(15,23,42,.02);
  }

  .audit-badge{
    display:inline-flex;
    align-items:center;
    padding: 5px 10px;
    border-radius: 999px;
    border: 1px solid rgba(15,23,42,.12);
    background: rgba(15,23,42,.03);
    color: rgba(15,23,42,.90);
    font-weight: 950;
  }

  .audit-details{
    background: rgba(15,23,42,.02);
    border-top: 1px solid var(--audit-border);
  }

  .audit-pre{
    white-space: pre-wrap;
    word-break: break-word;
    border-radius: 14px;
    padding: 14px;
    font-size: 12px;
    line-height: 1.35;
    background: #f8fafc;
    color: #0f172a;
    border: 1px solid rgba(15,23,42,.10);
  }

  /* Paginação (Light mantém bootstrap padrão) */
  .audit-pagination .pagination{ margin-bottom:0; }


  /* ==========================================================
     ✅ DARK — só ativa quando selecionar o tema Dark
     (mantém seu Light intacto)
     ========================================================== */
  html[data-theme="dark"] .audit-wrap{
    --audit-surface: rgba(10,14,20,.80);
    --audit-surface-2: rgba(10,14,20,.75);
    --audit-border: rgba(255,255,255,.10);
    --audit-border-2: rgba(255,255,255,.08);
    --audit-text: rgba(231,237,246,.92);
    --audit-muted: rgba(231,237,246,.65);
    --audit-head: rgba(231,237,246,.86);
    --audit-shadow: 0 18px 55px rgba(0,0,0,.45);
    --audit-hero-shadow: 0 18px 55px rgba(0,0,0,.55);
  }

  html[data-theme="dark"] .audit-hero__bg{
    display:block;
    position:absolute; inset:-40px;
    background:
      radial-gradient(900px 320px at 15% 20%, rgba(59,130,246,.18), transparent 60%),
      radial-gradient(700px 260px at 85% 30%, rgba(16,185,129,.14), transparent 55%),
      radial-gradient(800px 320px at 60% 120%, rgba(168,85,247,.10), transparent 60%);
    filter: blur(10px);
    opacity: .9;
    pointer-events:none;
  }

  html[data-theme="dark"] .audit-pill{
    background: rgba(15,20,28,.55);
    border-color: rgba(255,255,255,.10);
    color: rgba(231,237,246,.86);
  }
  html[data-theme="dark"] .audit-pill.is-ok{
    background: rgba(16,185,129,.10);
    border-color: rgba(16,185,129,.25);
    color: rgba(231,237,246,.92);
  }

  html[data-theme="dark"] .audit-table thead th{
    background: rgba(15,20,28,.75);
    border-bottom-color: rgba(255,255,255,.10) !important;
  }
  html[data-theme="dark"] .audit-table tbody td{
    border-top-color: rgba(255,255,255,.08) !important;
  }
  html[data-theme="dark"] .audit-table tbody tr:hover td{
    background: rgba(255,255,255,.03);
  }

  html[data-theme="dark"] .audit-badge{
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.12);
    color: rgba(231,237,246,.92);
  }

  html[data-theme="dark"] .audit-details{
    background: rgba(15,20,28,.55);
    border-top-color: rgba(255,255,255,.10);
  }

  html[data-theme="dark"] .audit-pre{
    background: #0f172a;
    color: #e2e8f0;
    border-color: rgba(255,255,255,.10);
  }

  /* Paginação (Dark no estilo “pílulas”) */
  html[data-theme="dark"] .audit-pagination .pagination{
    gap: 6px;
  }
  html[data-theme="dark"] .audit-pagination .page-link{
    border-radius: 10px !important;
    border: 1px solid rgba(255,255,255,.10) !important;
    background: rgba(10,14,20,.70) !important;
    color: rgba(231,237,246,.88) !important;
    font-weight: 900;
  }
  html[data-theme="dark"] .audit-pagination .page-item.active .page-link{
    background: rgba(59,130,246,.85) !important;
    border-color: rgba(59,130,246,.40) !important;
    color: #fff !important;
  }
  html[data-theme="dark"] .audit-pagination .page-item.disabled .page-link{
    opacity: .45;
  }

  /* ==========================================================
   FIX: no DARK alguns textos ficam pretos (bootstrap/text-dark)
   ========================================================== */
html[data-theme="dark"] .audit-wrap,
html[data-theme="dark"] .audit-wrap *{
  color-scheme: dark;
}

html[data-theme="dark"] .audit-wrap,
html[data-theme="dark"] .audit-wrap .audit-card,
html[data-theme="dark"] .audit-wrap .audit-hero,
html[data-theme="dark"] .audit-wrap .audit-table,
html[data-theme="dark"] .audit-wrap .audit-table td,
html[data-theme="dark"] .audit-wrap .audit-table th{
  color: rgba(231,237,246,.92) !important;
}

/* textos “muted” no dark */
html[data-theme="dark"] .audit-wrap .audit-muted,
html[data-theme="dark"] .audit-wrap .text-muted{
  color: rgba(231,237,246,.65) !important;
}

/* caso exista text-dark em algum lugar */
html[data-theme="dark"] .audit-wrap .text-dark{
  color: rgba(231,237,246,.92) !important;
}

/* links dentro da tabela */
html[data-theme="dark"] .audit-wrap a{
  color: rgba(231,237,246,.92) !important;
}
html[data-theme="dark"] .audit-wrap a:hover{
  color: rgba(255,255,255,1) !important;
}

/* campos/select no dark (texto e fundo) */
html[data-theme="dark"] .audit-wrap .form-control,
html[data-theme="dark"] .audit-wrap .form-select{
  background: rgba(10,14,20,.55) !important;
  border-color: rgba(255,255,255,.10) !important;
  color: rgba(231,237,246,.92) !important;
}
html[data-theme="dark"] .audit-wrap .form-control::placeholder{
  color: rgba(231,237,246,.55) !important;
}
</style>

<script>
  // ✅ filtrar ações por grupo (sem plugin)
  document.addEventListener('DOMContentLoaded', () => {
    const groupSel = document.getElementById('acaoGroup');
    const actionSel = document.getElementById('acaoSelect');
    if (!groupSel || !actionSel) return;

    const allOptgroups = Array.from(actionSel.querySelectorAll('optgroup'));

    const showGroup = (gid) => {
      allOptgroups.forEach(g => {
        const ok = !gid || (g.getAttribute('data-group') === gid);
        g.disabled = !ok;
        g.style.display = ok ? '' : 'none';
      });

      const selected = actionSel.value || '';
      if (gid && selected) {
        const existsInGroup = actionSel.querySelector(`optgroup[data-group="${gid}"] option[value="${CSS.escape(selected)}"]`);
        if (!existsInGroup) actionSel.value = '';
      }
    };

    // ✅ começa no grupo correto (detectado no PHP)
    const initialGroup = @json($selectedGroup);
    if (initialGroup) groupSel.value = initialGroup;

    showGroup(groupSel.value);
    groupSel.addEventListener('change', () => showGroup(groupSel.value));
  });

  // Copiar detalhes
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-copy]');
    if (!btn) return;

    const sel = btn.getAttribute('data-copy');
    const el = document.querySelector(sel);
    if (!el) return;

    try {
      await navigator.clipboard.writeText(el.innerText || el.textContent || '');
      const old = btn.innerText;
      btn.innerText = 'Copiado!';
      setTimeout(() => btn.innerText = old, 900);
    } catch (err) {
      alert('Não foi possível copiar.');
    }
  });

  // Quando está protegido: clicar em "Detalhes" abre o modal
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.audit-need-pass');
    if (!btn) return;

    const target = btn.getAttribute('data-target') || '';
    const logId = btn.getAttribute('data-log-id') || '';

    const openAfter = document.getElementById('auditOpenAfter');
    if (openAfter) openAfter.value = target;

    const logIdEl = document.getElementById('auditLogId');
    if (logIdEl) logIdEl.value = logId;

    const modalEl = document.getElementById('auditUnlockModal');
    if (!modalEl) return;

    if (window.bootstrap && bootstrap.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else {
      alert('Bootstrap JS não carregado (modal não disponível).');
    }
  });

  // Auto-abrir detalhe após desbloquear
  document.addEventListener('DOMContentLoaded', () => {
    const openAfter = @json($openAfter);
    const unlocked = @json($unlocked);

    if (!unlocked || !openAfter) return;

    const targetSel = String(openAfter).trim();
    const el = targetSel ? document.querySelector(targetSel) : null;
    if (!el) return;

    if (window.bootstrap && bootstrap.Collapse) {
      const c = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
      c.show();
      setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'center' }), 150);
    } else {
      el.classList.add('show');
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });

  // TTL real: quando expirar recarrega
  document.addEventListener('DOMContentLoaded', () => {
    const unlocked = @json($unlocked);
    const untilTs = @json($unlockedUntilTs);

    if (!unlocked || !untilTs) return;

    const now = Math.floor(Date.now() / 1000);
    const msLeft = (untilTs - now) * 1000;

    if (msLeft <= 0) {
      window.location.reload();
      return;
    }

    setTimeout(() => window.location.reload(), msLeft + 250);
  });
</script>
@endsection