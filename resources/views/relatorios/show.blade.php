@extends('layouts.app')

@section('content')
@php
  $normRg = function($v){
    $rg = preg_replace('/\D+/', '', (string)($v ?? ''));
    return $rg !== '' ? $rg : null;
  };

  $userByRg = function($rg) use ($usersByRg){
    if(!$rg || !isset($usersByRg)) return null;
    return $usersByRg[$rg] ?? null;
  };

  $fmtUser = function($u, $rg){
    if($u){
      return $u->name
        . ' <span class="text-muted">(' . e($u->cargo ?? '—') . ')</span>'
        . ' • <span class="font-monospace">' . e($u->rg ?? $rg ?? '—') . '</span>';
    }
    return '<span class="font-monospace">' . e($rg ?? '—') . '</span>';
  };

  $st = (string)($relatorio->status ?? 'pendente');

  $isEmPatrulha = $st === 'em_patrulha';
  $isPendente   = $st === 'pendente';
  $isAprovado   = $st === 'aprovado';
  $isReprovado  = $st === 'reprovado';

  $statusLabel = $isAprovado
    ? 'Aprovado'
    : ($isReprovado
      ? 'Reprovado'
      : ($isEmPatrulha ? 'Unidade em patrulha' : 'Pendente'));

  $statusClass = $isAprovado
    ? 'rep-badge-status rep-badge--ok'
    : ($isReprovado
      ? 'rep-badge-status rep-badge--bad'
      : ($isEmPatrulha
        ? 'rep-badge-status rep-badge--info'
        : 'rep-badge-status rep-badge--warn'));

  $dataFmt = !empty($relatorio->data_patrulhamento)
    ? \Carbon\Carbon::parse($relatorio->data_patrulhamento)->format('d/m/Y')
    : '—';

  $cRG  = $normRg($relatorio->qra_chefe ?? null);
  $mRG  = $normRg($relatorio->motorista ?? null);
  $p3RG = $normRg($relatorio->terceiro ?? null);
  $p4RG = $normRg($relatorio->quarto ?? null);
  $p5RG = $normRg($relatorio->quinto ?? null);

  $cU  = $userByRg($cRG);
  $mU  = $userByRg($mRG);
  $p3U = $userByRg($p3RG);
  $p4U = $userByRg($p4RG);
  $p5U = $userByRg($p5RG);

  $inicioFmt = !empty($relatorio->inicio_patrulhamento)
    ? \Carbon\Carbon::parse($relatorio->inicio_patrulhamento)->format('H:i')
    : '—';

  $finalFmt = !empty($relatorio->final_patrulhamento)
    ? \Carbon\Carbon::parse($relatorio->final_patrulhamento)->format('H:i')
    : null;

  $obsDecisao  = $relatorio->decisao_obs ?? $relatorio->observacao ?? null;

  $aprovadoPor  = $relatorio->aprovado_por ?? null;
  $reprovadoPor = $relatorio->reprovado_por ?? null;

  $apU = ($aprovadoPor && isset($usersById)) ? ($usersById[(int)$aprovadoPor] ?? null) : null;
  $rpU = ($reprovadoPor && isset($usersById)) ? ($usersById[(int)$reprovadoPor] ?? null) : null;

  $fmtById = function($u, $id){
    if($u){
      return $u->name
        . ' <span class="text-muted">(' . e($u->cargo ?? '—') . ')</span>'
        . ' • <span class="font-monospace">' . e($u->rg ?? '—') . '</span>';
    }
    return 'ID <span class="font-monospace">' . e($id) . '</span>';
  };

  $authRow = auth()->check();
  $nivel = $authRow ? (int)(auth()->user()->nivel ?? 0) : 0;

  $isOwner = $authRow && (int)$relatorio->user_id === (int)auth()->id();

  $editable = $isEmPatrulha
    && empty($relatorio->final_patrulhamento)
    && $isOwner;

  $canForceClose = $authRow
    && $nivel >= 7
    && $isEmPatrulha
    && empty($relatorio->final_patrulhamento);

  $canCloseTurno = $editable || $canForceClose;

  $canDecide = $authRow && auth()->user()->can('decide', $relatorio);
  $blockedByGuarnicao = $authRow && $nivel >= 6 && !$canDecide;
  $canDecisionNow = !$isEmPatrulha && $isPendente && !$editable && $canDecide;

  $backUrl = request('back', route('relatorios.index'));

  $bopmRegistrosRaw = old('bopm_registros');
  if (!is_array($bopmRegistrosRaw)) {
      $bopmRegistrosRaw = data_get($relatorio, 'bopm_registros', []);
      if (is_string($bopmRegistrosRaw)) {
          $decoded = json_decode($bopmRegistrosRaw, true);
          $bopmRegistrosRaw = is_array($decoded) ? $decoded : [];
      }
      if (!is_array($bopmRegistrosRaw)) {
          $bopmRegistrosRaw = [];
      }
  }
  $bopmRegistrosRaw = array_values(array_map(fn($v) => trim((string)$v), $bopmRegistrosRaw));
@endphp

<style>
  .rep-show{
    --rep-radius-2xl: 26px;
    --rep-radius-xl: 22px;
    --rep-radius-lg: 18px;
    --rep-radius-md: 14px;
    --rep-radius-sm: 12px;

    --rep-card: #ffffff;
    --rep-card-2: #f8fafc;
    --rep-card-3: #f1f5f9;
    --rep-border: rgba(15, 23, 42, .08);
    --rep-border-2: rgba(15, 23, 42, .06);
    --rep-text: #0f172a;
    --rep-muted: #64748b;
    --rep-muted-2: #94a3b8;
    --rep-primary: #2563eb;
    --rep-primary-2: #1d4ed8;
    --rep-success: #059669;
    --rep-warning: #d97706;
    --rep-danger: #dc2626;
    --rep-info: #0284c7;
    --rep-shadow: 0 20px 60px rgba(15, 23, 42, .10);
    --rep-shadow-soft: 0 10px 30px rgba(15, 23, 42, .08);
    --rep-shadow-inset: inset 0 1px 0 rgba(255,255,255,.55);
  }

  html[data-theme="dark"] .rep-show{
    --rep-card: rgba(255,255,255,.06);
    --rep-card-2: rgba(255,255,255,.04);
    --rep-card-3: rgba(255,255,255,.05);
    --rep-border: rgba(255,255,255,.12);
    --rep-border-2: rgba(255,255,255,.10);
    --rep-text: rgba(231,237,246,.92);
    --rep-muted: rgba(231,237,246,.62);
    --rep-muted-2: rgba(231,237,246,.46);
    --rep-primary: #5aa2ff;
    --rep-primary-2: #3b82f6;
    --rep-success: #10b981;
    --rep-warning: #f59e0b;
    --rep-danger: #ef4444;
    --rep-info: #38bdf8;
    --rep-shadow: 0 18px 55px rgba(0,0,0,.55);
    --rep-shadow-soft: 0 14px 40px rgba(0,0,0,.45);
    --rep-shadow-inset: none;
  }

  .rep-shell{
    max-width: 1080px;
    margin: 0 auto;
    padding: 10px 12px 24px;
  }

  .rep-hero{
    position: relative;
    overflow: hidden;
    border-radius: var(--rep-radius-2xl);
    border: 1px solid var(--rep-border);
    background:
      radial-gradient(900px 420px at 0% 0%, rgba(37,99,235,.12), transparent 55%),
      radial-gradient(720px 340px at 100% 10%, rgba(16,185,129,.08), transparent 55%),
      linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.98));
    box-shadow: var(--rep-shadow);
    margin-bottom: 16px;
  }

  html[data-theme="dark"] .rep-hero{
    background: var(--rep-card);
  }

  .rep-hero__bg{
    position:absolute;
    inset:0;
    pointer-events:none;
    background:
      radial-gradient(1000px 520px at 18% 18%, rgba(37,99,235,.14), transparent 60%),
      radial-gradient(900px 520px at 85% 18%, rgba(16,185,129,.10), transparent 55%),
      radial-gradient(800px 520px at 50% 120%, rgba(2,6,23,.10), transparent 60%);
  }

  html[data-theme="dark"] .rep-hero__bg{
    background:
      radial-gradient(1000px 520px at 20% 20%, rgba(90,162,255,.22), transparent 60%),
      radial-gradient(900px 520px at 85% 25%, rgba(16,185,129,.14), transparent 55%),
      radial-gradient(800px 520px at 50% 115%, rgba(0,0,0,.65), transparent 60%),
      linear-gradient(180deg, rgba(8,13,20,.25), rgba(8,13,20,.88));
  }

  .rep-hero__content{
    position: relative;
    z-index: 1;
    padding: 22px;
    display:grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(280px, .85fr);
    gap: 18px;
    align-items: stretch;
  }

  @media (max-width: 992px){
    .rep-hero__content{ grid-template-columns: 1fr; }
  }

  .rep-kicker{
    font-size: 11px;
    font-weight: 950;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: var(--rep-muted);
  }

  .rep-title{
    margin: 8px 0 8px;
    font-size: clamp(28px, 3vw, 36px);
    line-height: 1.04;
    font-weight: 950;
    letter-spacing: -.05em;
    color: var(--rep-text);
  }

  .rep-sub{
    color: var(--rep-muted);
    font-size: 14px;
    font-weight: 700;
    max-width: 760px;
    line-height: 1.55;
  }

  .rep-badges{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:14px;
  }

  .rep-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid rgba(37,99,235,.16);
    background: rgba(37,99,235,.08);
    color: var(--rep-text);
    font-size: 12px;
    font-weight: 900;
    box-shadow: var(--rep-shadow-inset);
  }

  html[data-theme="dark"] .rep-badge{
    border-color: rgba(90,162,255,.26);
    background: rgba(90,162,255,.12);
  }

  .rep-badge--soft{
    border-color: var(--rep-border);
    background: rgba(255,255,255,.7);
    color: var(--rep-muted);
  }

  html[data-theme="dark"] .rep-badge--soft{
    background: rgba(255,255,255,.06);
    color: rgba(231,237,246,.74);
  }

  .rep-badge__dot{
    width:8px;
    height:8px;
    border-radius:50%;
    background: var(--rep-primary);
    box-shadow: 0 0 0 4px rgba(37,99,235,.14);
  }

  .rep-mini{
    height: 100%;
    border-radius: 18px;
    border: 1px solid var(--rep-border);
    background: rgba(255,255,255,.75);
    box-shadow: var(--rep-shadow-soft);
    overflow: hidden;
    backdrop-filter: blur(10px);
  }

  html[data-theme="dark"] .rep-mini{
    background: rgba(15,20,28,.55);
  }

  .rep-mini__item{
    padding: 14px 15px;
    border-top: 1px solid var(--rep-border-2);
  }

  .rep-mini__item:first-child{ border-top: 0; }

  .rep-mini__k{
    font-size: 11px;
    font-weight: 950;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--rep-muted);
  }

  .rep-mini__v{
    margin-top: 4px;
    font-size: 13px;
    line-height: 1.5;
    font-weight: 800;
    color: var(--rep-text);
  }

  .rep-card{
    border: 1px solid var(--rep-border);
    background: var(--rep-card);
    border-radius: var(--rep-radius-2xl);
    box-shadow: var(--rep-shadow);
    overflow: hidden;
  }

  .rep-card__head{
    padding: 16px 18px;
    border-bottom: 1px solid var(--rep-border);
    background:
      linear-gradient(180deg, rgba(148,163,184,.06), rgba(148,163,184,.03));
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 12px;
    flex-wrap: wrap;
  }

  .rep-card__title{
    font-size: 18px;
    font-weight: 950;
    letter-spacing: -.03em;
    color: var(--rep-text);
  }

  .rep-card__sub{
    margin-top: 4px;
    font-size: 13px;
    font-weight: 700;
    color: var(--rep-muted);
  }

  .rep-card__body{
    padding: 18px;
  }

  .rep-toolbar{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:center;
  }

  .rep-btn{
    border-radius: 14px !important;
    min-height: 46px;
    padding-inline: 16px;
    font-weight: 900 !important;
    box-shadow: var(--rep-shadow-soft);
  }

  .rep-btn-primary{
    background: linear-gradient(135deg, var(--rep-primary), var(--rep-primary-2)) !important;
    border-color: transparent !important;
  }

  .rep-btn-success{
    background: linear-gradient(135deg, #10b981, #059669) !important;
    border-color: transparent !important;
  }

  .rep-section{
    margin-bottom: 18px;
    border: 1px solid var(--rep-border);
    border-radius: 20px;
    background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.94));
    box-shadow: var(--rep-shadow-soft);
    overflow: hidden;
  }

  html[data-theme="dark"] .rep-section{
    background: linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.035));
  }

  .rep-section__head{
    padding: 14px 16px;
    border-bottom: 1px solid var(--rep-border);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 12px;
    flex-wrap: wrap;
    background: rgba(148,163,184,.05);
  }

  .rep-section__title{
    font-size: 15px;
    font-weight: 950;
    color: var(--rep-text);
    letter-spacing: -.02em;
  }

  .rep-section__sub{
    font-size: 12px;
    font-weight: 800;
    color: var(--rep-muted);
  }

  .rep-section__body{
    padding: 16px;
  }

  .rep-field-k{
    font-size: 12px;
    font-weight: 900;
    color: var(--rep-muted);
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 5px;
  }

  .rep-field-v{
    font-size: 14px;
    font-weight: 800;
    color: var(--rep-text);
    line-height: 1.55;
  }

  .rep-soft-box{
    padding: 14px;
    border-radius: 16px;
    border: 1px solid var(--rep-border);
    background: rgba(255,255,255,.70);
    box-shadow: var(--rep-shadow-soft);
  }

  html[data-theme="dark"] .rep-soft-box{
    background: rgba(15,20,28,.62);
  }

  .rep-form .form-control,
  .rep-form textarea{
    border-radius: 14px !important;
    min-height: 48px;
    border: 1px solid var(--rep-border) !important;
    background: rgba(255,255,255,.94) !important;
    color: var(--rep-text) !important;
    font-weight: 700;
    box-shadow: none !important;
  }

  html[data-theme="dark"] .rep-form .form-control,
  html[data-theme="dark"] .rep-form textarea{
    background: rgba(15,23,42,.82) !important;
    border-color: rgba(255,255,255,.12) !important;
  }

  .rep-form .form-control:focus,
  .rep-form textarea:focus{
    border-color: rgba(37,99,235,.45) !important;
    box-shadow: 0 0 0 4px rgba(37,99,235,.10) !important;
  }

  html[data-theme="dark"] .rep-form .form-control:focus,
  html[data-theme="dark"] .rep-form textarea:focus{
    border-color: rgba(90,162,255,.55) !important;
    box-shadow: 0 0 0 4px rgba(90,162,255,.16) !important;
  }

  .rep-badge-status{
    display:inline-flex;
    align-items:center;
    gap:8px;
    width: fit-content;
    padding:7px 11px;
    border-radius:999px;
    font-size:12px;
    line-height:1;
    font-weight:950;
    letter-spacing:.02em;
    white-space:nowrap;
    border: 1px solid transparent;
  }

  .rep-badge-status .rep-dot{
    width:8px;
    height:8px;
    border-radius:99px;
    flex: 0 0 auto;
  }

  .rep-badge--info{
    background: rgba(14,165,233,.10) !important;
    color: #0369a1 !important;
    border-color: rgba(14,165,233,.22);
  }
  .rep-badge--info .rep-dot{
    background: var(--rep-info);
    box-shadow: 0 0 0 3px rgba(14,165,233,.16);
  }

  .rep-badge--warn{
    background: rgba(245,158,11,.12) !important;
    color: #b45309 !important;
    border-color: rgba(245,158,11,.22);
  }
  .rep-badge--warn .rep-dot{
    background: var(--rep-warning);
    box-shadow: 0 0 0 3px rgba(245,158,11,.16);
  }

  .rep-badge--ok{
    background: rgba(16,185,129,.12) !important;
    color: #047857 !important;
    border-color: rgba(16,185,129,.22);
  }
  .rep-badge--ok .rep-dot{
    background: var(--rep-success);
    box-shadow: 0 0 0 3px rgba(16,185,129,.16);
  }

  .rep-badge--bad{
    background: rgba(239,68,68,.10) !important;
    color: #b91c1c !important;
    border-color: rgba(239,68,68,.18);
  }
  .rep-badge--bad .rep-dot{
    background: var(--rep-danger);
    box-shadow: 0 0 0 3px rgba(239,68,68,.16);
  }

  html[data-theme="dark"] .rep-badge--info{
    background: rgba(14,165,233,.12) !important;
    color: rgba(200,240,255,.92) !important;
    border-color: rgba(14,165,233,.28);
  }
  html[data-theme="dark"] .rep-badge--warn{
    background: rgba(245,158,11,.14) !important;
    color: rgba(255,220,170,.92) !important;
    border-color: rgba(245,158,11,.30);
  }
  html[data-theme="dark"] .rep-badge--ok{
    background: rgba(16,185,129,.12) !important;
    color: rgba(190,255,230,.92) !important;
    border-color: rgba(16,185,129,.26);
  }
  html[data-theme="dark"] .rep-badge--bad{
    background: rgba(239,68,68,.12) !important;
    color: rgba(255,200,200,.92) !important;
    border-color: rgba(239,68,68,.26);
  }

  .rep-note{
    font-size: 12px;
    color: var(--rep-muted);
    font-weight: 800;
    line-height: 1.45;
  }

  .rep-autosave{
    margin-top: 8px;
    font-size: 12px;
    font-weight: 800;
    color: var(--rep-muted);
  }

  details.rep-section > summary.rep-section__head{
    list-style: none;
    cursor: pointer;
    user-select: none;
  }

  details.rep-section > summary.rep-section__head::-webkit-details-marker{
    display: none;
  }

  .rep-summary__row{
    width: 100%;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 12px;
  }

  .rep-summary__chev{
    opacity: .75;
    transition: transform .15s ease;
  }

  details[open] .rep-summary__chev{
    transform: rotate(180deg);
  }

  .grr-callout{
    display:flex;
    gap: 12px;
    align-items:center;
    padding: 12px 12px;
    border-radius: 16px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(255,255,255,.70);
    box-shadow: 0 12px 30px rgba(2,6,23,.08);
  }

  .grr-callout__ico{
    width: 40px;
    height: 40px;
    display:grid;
    place-items:center;
    border-radius: 999px;
    font-weight: 900;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(2,6,23,.03);
  }

  .grr-callout__title{
    font-weight: 950;
    letter-spacing: .2px;
    line-height: 1.1;
  }

  .grr-callout__sub{
    font-size: 12px;
    font-weight: 700;
    color: rgba(2,6,23,.60);
    margin-top: 2px;
  }

  html[data-theme="dark"] .grr-callout{
    border-color: rgba(255,255,255,.10);
    background: rgba(15,20,28,.62);
    backdrop-filter: blur(10px);
    box-shadow: 0 18px 55px rgba(0,0,0,.55);
    color: rgba(231,237,246,.90);
  }

  html[data-theme="dark"] .grr-callout__ico{
    border-color: rgba(255,255,255,.12);
    background: rgba(255,255,255,.06);
    color: rgba(231,237,246,.92);
  }

  html[data-theme="dark"] .grr-callout__sub{
    color: rgba(231,237,246,.68);
  }

  .grr-decision__title{
    font-weight: 950;
    letter-spacing: .2px;
    margin-bottom: 10px;
    color: var(--rep-text);
  }

  .grr-decision__box{
    padding: 14px 14px;
    border-radius: 16px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(255,255,255,.70);
    box-shadow: 0 12px 30px rgba(2,6,23,.08);
    color: rgba(2,6,23,.82);
  }

  html[data-theme="dark"] .grr-decision__title{
    color: rgba(231,237,246,.92);
  }

  html[data-theme="dark"] .grr-decision__box{
    border-color: rgba(255,255,255,.10);
    background: rgba(15,20,28,.62);
    backdrop-filter: blur(10px);
    box-shadow: 0 18px 55px rgba(0,0,0,.55);
    color: rgba(231,237,246,.86);
  }

  .grr-auditline{
    display:flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: baseline;
    font-weight: 700;
    color: rgba(2,6,23,.70);
  }

  .grr-auditline__k{
    font-weight: 950;
    color: rgba(2,6,23,.82);
  }

  .grr-auditline__v{
    color: rgba(2,6,23,.70);
  }

  html[data-theme="dark"] .grr-auditline{
    color: rgba(231,237,246,.74);
  }

  html[data-theme="dark"] .grr-auditline__k{
    color: rgba(231,237,246,.92);
  }

  html[data-theme="dark"] .grr-auditline__v{
    color: rgba(231,237,246,.78);
  }

  .grr-pager{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 14px;
    padding: 12px 14px;
    border-radius: 18px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(255,255,255,.72);
    box-shadow: 0 14px 40px rgba(2,6,23,.08);
  }

  .grr-pager__left{
    display:flex;
    align-items:center;
    gap: 10px;
    font-weight: 800;
    color: rgba(2,6,23,.72);
    white-space: nowrap;
  }

  .grr-pager__dot{
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: rgba(16,185,129,.85);
    box-shadow: 0 0 0 4px rgba(16,185,129,.12);
    flex: 0 0 auto;
  }

  .grr-pager__right{
    display:flex;
    align-items:center;
    gap: 12px;
    flex-wrap: wrap;
    justify-content:flex-end;
    width: 100%;
  }

  .grr-pager__meta{
    font-size: 12px;
    font-weight: 800;
    color: rgba(2,6,23,.58);
    white-space: nowrap;
  }

  .grr-pager__links .pagination{
    margin: 0;
    gap: 6px;
  }

  .grr-pager__links .page-link{
    border-radius: 10px !important;
    font-weight: 900;
    padding: 8px 12px;
    border: 1px solid rgba(2,6,23,.12);
    color: rgba(2,6,23,.78);
    background: rgba(255,255,255,.70);
  }

  .grr-pager__links .page-item.active .page-link{
    background: rgba(13,110,253,.95) !important;
    border-color: rgba(13,110,253,.85) !important;
    color: #fff !important;
    box-shadow: 0 10px 24px rgba(13,110,253,.22);
  }

  .grr-pager__links .page-item.disabled .page-link{
    opacity: .55;
    cursor: not-allowed;
  }

  html[data-theme="dark"] .grr-pager{
    border-color: rgba(255,255,255,.10);
    background: rgba(15,20,28,.62);
    backdrop-filter: blur(10px);
    box-shadow: 0 18px 60px rgba(0,0,0,.60);
  }

  html[data-theme="dark"] .grr-pager__left{
    color: rgba(231,237,246,.84);
  }

  html[data-theme="dark"] .grr-pager__meta{
    color: rgba(231,237,246,.62);
  }

  html[data-theme="dark"] .grr-pager__links .page-link{
    border-color: rgba(255,255,255,.12);
    background: rgba(12,16,24,.92);
    color: rgba(231,237,246,.86);
  }

  html[data-theme="dark"] .grr-pager__links .page-link:hover{
    border-color: rgba(255,255,255,.18);
    background: rgba(15,20,28,.92);
  }

  .rep-obs-input{
    border-radius: 14px !important;
    background: rgba(255,255,255,.92) !important;
    border: 1px solid var(--rep-border) !important;
    color: var(--rep-text) !important;
    font-weight: 700;
    min-height: 120px;
  }

  html[data-theme="dark"] .rep-obs-input{
    background: rgba(15,23,42,.82) !important;
    border-color: rgba(255,255,255,.12) !important;
  }

  .rep-obs-input:focus{
    border-color: rgba(37,99,235,.45) !important;
    box-shadow: 0 0 0 4px rgba(37,99,235,.10) !important;
  }

  html[data-theme="dark"] .rep-obs-input:focus{
    border-color: rgba(90,162,255,.55) !important;
    box-shadow: 0 0 0 4px rgba(90,162,255,.16) !important;
  }

  .rep-obs-meta{
    margin-top: 8px;
    display:flex;
    justify-content:space-between;
    gap:10px;
    font-size:12px;
    font-weight:800;
    color: var(--rep-muted);
  }

  .rep-money-group .input-group-text,
  .rep-bopm-group .input-group-text{
    border-radius: 14px 0 0 14px !important;
    border: 1px solid var(--rep-border) !important;
    border-right: 0 !important;
    background: rgba(255,255,255,.92) !important;
    color: var(--rep-text) !important;
    font-weight: 900;
    min-height: 48px;
  }

  .rep-money-group .form-control,
  .rep-bopm-group .form-control{
    border-left: 0 !important;
    border-radius: 0 14px 14px 0 !important;
  }

  html[data-theme="dark"] .rep-money-group .input-group-text,
  html[data-theme="dark"] .rep-bopm-group .input-group-text{
    background: rgba(15,23,42,.82) !important;
    border-color: rgba(255,255,255,.12) !important;
    color: rgba(231,237,246,.92) !important;
  }

  .rep-bopm-registros{
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px dashed var(--rep-border);
  }

  .rep-bopm-registros.is-hidden{
    display: none;
  }

  .rep-bopm-registros__title{
    font-size: 12px;
    font-weight: 900;
    color: var(--rep-muted);
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 10px;
  }

  .rep-bopm-registro-card{
    padding: 12px;
    border-radius: 16px;
    border: 1px solid var(--rep-border);
    background: rgba(255,255,255,.70);
    box-shadow: var(--rep-shadow-soft);
  }

  html[data-theme="dark"] .rep-bopm-registro-card{
    background: rgba(15,20,28,.62);
  }

  .rep-bopm-help{
    margin-top: 8px;
    font-size: 12px;
    font-weight: 800;
    color: var(--rep-muted);
  }

  .turno-loading-overlay{
    position: fixed;
    inset: 0;
    z-index: 20000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: rgba(15, 23, 42, .72);
    backdrop-filter: blur(10px);
  }

  .turno-loading-overlay.d-none{
    display: none !important;
  }

  .turno-loading-card{
    width: min(100%, 420px);
    border-radius: 24px;
    padding: 28px 24px;
    text-align: center;
    border: 1px solid rgba(255,255,255,.14);
    background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.94));
    box-shadow: 0 28px 80px rgba(0,0,0,.28);
  }

  html[data-theme="dark"] .turno-loading-card{
    background: linear-gradient(180deg, rgba(11,18,32,.98), rgba(8,14,26,.98));
    border-color: rgba(255,255,255,.12);
    box-shadow: 0 28px 80px rgba(0,0,0,.55);
  }

  .turno-loading-spinner{
    width: 58px;
    height: 58px;
    margin: 0 auto 16px;
    border-radius: 50%;
    border: 4px solid rgba(37,99,235,.18);
    border-top-color: #2563eb;
    animation: turnoSpin .8s linear infinite;
  }

  html[data-theme="dark"] .turno-loading-spinner{
    border-color: rgba(90,162,255,.18);
    border-top-color: #5aa2ff;
  }

  .turno-loading-title{
    font-size: 20px;
    font-weight: 950;
    letter-spacing: -.02em;
    color: var(--rep-text);
  }

  .turno-loading-sub{
    margin-top: 8px;
    font-size: 13px;
    line-height: 1.55;
    font-weight: 700;
    color: var(--rep-muted);
  }

  .turno-btn-spinner{
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,.28);
    border-top-color: rgba(255,255,255,1);
    border-radius: 50%;
    display: inline-block;
    animation: turnoSpin .8s linear infinite;
    vertical-align: -3px;
    margin-right: 8px;
  }

  body.turno-is-submitting{
    cursor: wait;
  }

  body.turno-is-submitting *{
    cursor: wait !important;
  }

  @keyframes turnoSpin{
    to{ transform: rotate(360deg); }
  }

  @media (max-width: 900px){
    .grr-pager{
      flex-direction: column;
      align-items: flex-start;
    }
    .grr-pager__right{
      justify-content:flex-start;
    }
  }

  @media (max-width: 768px){
    .rep-shell{ padding-inline: 8px; }
    .rep-title{ font-size: 25px; }
    .rep-toolbar .rep-btn{ width: 100%; justify-content: center; }
    .rep-obs-meta{ flex-direction: column; align-items: flex-start; }
  }
</style>

<div class="rep-show">
  <div class="rep-shell">

    <div class="rep-hero">
      <div class="rep-hero__bg"></div>

      <div class="rep-hero__content">
        <div>
          <div class="rep-kicker">GRR • PRF — Visualização Operacional</div>
          <h1 class="rep-title">Relatório #{{ $relatorio->id }}</h1>
          <div class="rep-sub">
            Visualização completa do relatório de patrulhamento, com status, composição da equipe, resultados operacionais, auto-save em andamento e histórico de auditoria.
          </div>

          <div class="rep-badges">
            <span class="{{ $statusClass }}">
              <span class="rep-dot"></span>
              {{ $statusLabel }}
            </span>

            <span class="rep-badge rep-badge--soft">
              Data {{ $dataFmt }}
            </span>
          </div>

          @if($editable)
            <div class="rep-autosave">
              Auto-save: <span id="autosaveState" class="fw-bold">pronto</span>
            </div>
          @elseif($canForceClose)
            <div class="rep-autosave">
              Supervisão ativa: você pode encerrar este turno por permissão administrativa.
            </div>
          @endif
        </div>

        <div>
          <div class="rep-mini">
            <div class="rep-mini__item">
              <div class="rep-mini__k">Unidade</div>
              <div class="rep-mini__v">{{ $relatorio->unidade ?? '—' }}</div>
            </div>

            <div class="rep-mini__item">
              <div class="rep-mini__k">Horário</div>
              <div class="rep-mini__v">{{ $inicioFmt }} @if($finalFmt) até {{ $finalFmt }} @else • em andamento @endif</div>
            </div>

            <div class="rep-mini__item">
              <div class="rep-mini__k">Chefe da barca</div>
              <div class="rep-mini__v">{!! $fmtUser($cU, $cRG) !!}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="rep-card">
      <div class="rep-card__head">
        <div>
          <div class="rep-card__title">Painel do relatório</div>
          <div class="rep-card__sub">Consulta completa do relatório, com edição operacional apenas quando a unidade ainda estiver em patrulha.</div>
        </div>

        <div class="rep-toolbar">
          <a href="{{ $backUrl }}" class="btn btn-outline-secondary rep-btn">
            Voltar
          </a>

          @if(Route::has('dashboard'))
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary rep-btn">
              Dashboard
            </a>
          @endif

          @if($canCloseTurno)
            <form method="POST" action="{{ route('relatorios.encerrar_turno', $relatorio) }}" id="formEncerrarTurno">
              @csrf
              <button
                class="btn btn-success rep-btn rep-btn-success"
                id="btnEncerrarTurno"
                type="submit">
                Encerrar turno
              </button>
            </form>
          @endif
        </div>
      </div>

      <div class="rep-card__body">

        <section class="rep-section">
          <div class="rep-section__head">
            <div>
              <div class="rep-section__title">Guarnição</div>
              <div class="rep-section__sub">Informações principais da equipe e da unidade registrada no relatório.</div>
            </div>
            <div class="rep-section__sub">visão geral</div>
          </div>

          <div class="rep-section__body">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="rep-field-k">Unidade</div>
                <div class="rep-field-v">{{ $relatorio->unidade ?? '—' }}</div>
              </div>

              <div class="col-md-6">
                <div class="rep-field-k">Chefe da barca (RG)</div>
                <div class="rep-field-v">{!! $fmtUser($cU, $cRG) !!}</div>
              </div>
            </div>
          </div>
        </section>

        <details class="rep-section" open>
          <summary class="rep-section__head">
            <div class="rep-summary__row">
              <span class="rep-section__title">Equipe e horários</span>
              <span class="rep-summary__chev">▾</span>
            </div>
          </summary>

          <div class="rep-section__body">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="rep-field-k">Motorista (RG)</div>
                <div class="rep-field-v">{!! $fmtUser($mU, $mRG) !!}</div>
              </div>

              <div class="col-md-6">
                <div class="rep-field-k">Auxiliares</div>
                <div class="rep-field-v">
                  @php
                    $aux = [];
                    if($p3RG) $aux[] = 'P3: ' . strip_tags($fmtUser($p3U, $p3RG));
                    if($p4RG) $aux[] = 'P4: ' . strip_tags($fmtUser($p4U, $p4RG));
                    if($p5RG) $aux[] = 'P5: ' . strip_tags($fmtUser($p5U, $p5RG));
                  @endphp

                  @if(count($aux))
                    <div class="text-muted" style="line-height:1.55;">
                      @foreach($aux as $line)
                        <div>{{ $line }}</div>
                      @endforeach
                    </div>
                  @else
                    —
                  @endif
                </div>
              </div>

              <div class="col-md-6">
                <div class="rep-field-k">Início</div>
                <div class="rep-field-v">{{ $inicioFmt }}</div>
              </div>

              <div class="col-md-6">
                <div class="rep-field-k">Final</div>
                <div class="rep-field-v">
                  @if($finalFmt)
                    {{ $finalFmt }}
                  @else
                    @if($isEmPatrulha)
                      <span class="rep-badge-status rep-badge--info">
                        <span class="rep-dot"></span>
                        Em patrulha
                      </span>
                    @else
                      <span class="rep-badge rep-badge--soft">Em andamento</span>
                    @endif
                  @endif
                </div>
              </div>
            </div>
          </div>
        </details>

        <details class="rep-section" open>
          <summary class="rep-section__head">
            <div class="rep-summary__row">
              <span class="rep-section__title">Resultados operacionais</span>
              <span class="rep-summary__chev">▾</span>
            </div>
          </summary>

          <div class="rep-section__body">
            @if($editable)
              <div class="grr-callout mb-3">
                <div class="grr-callout__ico">💾</div>
                <div class="grr-callout__body">
                  <div class="grr-callout__title">Auto-save ativo</div>
                  <div class="grr-callout__sub">Ao preencher, o sistema salva automaticamente os dados do patrulhamento.</div>
                </div>
              </div>

              <form id="formRascunho" class="rep-form" autocomplete="off">
                <h6 class="fw-black mb-3" style="color: var(--rep-text);">Apreensões</h6>
                <div class="row g-3">
                  @php
                    $fieldsA = [
                      ['pistolas','Pistolas'],
                      ['smg_fuzil','SMG / Fuzil'],
                      ['municoes','Munições'],
                      ['drogas','Drogas'],
                      ['explosivos','Explosivos'],
                      ['lockpicks','Lockpicks'],
                    ];
                  @endphp

                  @foreach($fieldsA as [$k,$label])
                    <div class="col-md-3">
                      <label class="rep-field-k">{{ $label }}</label>
                      <input
                        type="number"
                        min="0"
                        step="1"
                        class="form-control"
                        name="{{ $k }}"
                        inputmode="numeric"
                        value="{{ is_null($relatorio->$k) ? '' : (int)$relatorio->$k }}"
                      >
                    </div>
                  @endforeach

                  <div class="col-md-3">
                    <label class="rep-field-k">Dinheiro</label>
                    <div class="input-group rep-money-group">
                      <span class="input-group-text">R$</span>
                      <input
                        type="number"
                        min="0"
                        step="1"
                        class="form-control"
                        name="dinheiro"
                        inputmode="numeric"
                        value="{{ is_null($relatorio->dinheiro) ? '' : (int)$relatorio->dinheiro }}"
                      >
                    </div>
                  </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-black mb-3" style="color: var(--rep-text);">Multas / Ações</h6>
                <div class="row g-3">
                  <div class="col-md-3">
                    <label class="rep-field-k">Abordagens</label>
                    <input
                      type="number"
                      min="0"
                      step="1"
                      class="form-control"
                      name="abordagens"
                      inputmode="numeric"
                      value="{{ is_null($relatorio->abordagens) ? '' : (int)$relatorio->abordagens }}"
                    >
                  </div>

                  <div class="col-md-3">
                    <label class="rep-field-k">Apoio</label>
                    <input
                      type="number"
                      min="0"
                      step="1"
                      class="form-control"
                      name="apoio"
                      inputmode="numeric"
                      value="{{ is_null($relatorio->apoio) ? '' : (int)$relatorio->apoio }}"
                    >
                  </div>

                  <div class="col-md-3">
                    <label class="rep-field-k">Incursão</label>
                    <input
                      type="number"
                      min="0"
                      step="1"
                      class="form-control"
                      name="incursao"
                      inputmode="numeric"
                      value="{{ is_null($relatorio->incursao) ? '' : (int)$relatorio->incursao }}"
                    >
                  </div>

                  <div class="col-md-3">
                    <label class="rep-field-k">Negociação</label>
                    <input
                      type="number"
                      min="0"
                      step="1"
                      class="form-control"
                      name="negociacao"
                      inputmode="numeric"
                      value="{{ is_null($relatorio->negociacao) ? '' : (int)$relatorio->negociacao }}"
                    >
                  </div>

                  <div class="col-md-3">
                    <label class="rep-field-k">Blitz</label>
                    <input
                      type="number"
                      min="0"
                      step="1"
                      class="form-control"
                      name="blitz"
                      inputmode="numeric"
                      value="{{ is_null($relatorio->blitz) ? '' : (int)$relatorio->blitz }}"
                    >
                  </div>

                  <div class="col-md-3">
                    <label class="rep-field-k">Escolta</label>
                    <input
                      type="number"
                      min="0"
                      step="1"
                      class="form-control"
                      name="escolta"
                      inputmode="numeric"
                      value="{{ is_null($relatorio->escolta) ? '' : (int)$relatorio->escolta }}"
                    >
                  </div>

                  <div class="col-md-3">
                    <label class="rep-field-k">Multas</label>
                    <input
                      type="number"
                      min="0"
                      step="1"
                      class="form-control"
                      name="multas"
                      inputmode="numeric"
                      value="{{ is_null($relatorio->multas) ? '' : (int)$relatorio->multas }}"
                    >
                  </div>

                  <div class="col-md-3">
                    <label class="rep-field-k">BOPM</label>
                    <input
                      type="number"
                      min="0"
                      step="1"
                      class="form-control"
                      id="inputBopm"
                      name="bopm"
                      inputmode="numeric"
                      value="{{ is_null($relatorio->bopm) ? '' : (int)$relatorio->bopm }}"
                    >
                  </div>

                  <div class="col-md-3">
                    <label class="rep-field-k">Viaturas fiscalizadas</label>
                    <input
                      type="number"
                      min="0"
                      step="1"
                      class="form-control"
                      name="viaturas_fiscalizadas"
                      inputmode="numeric"
                      value="{{ is_null($relatorio->viaturas_fiscalizadas) ? '' : (int)$relatorio->viaturas_fiscalizadas }}"
                    >
                  </div>
                </div>

                <div class="rep-bopm-registros {{ ((int)($relatorio->bopm ?? 0) > 0 || count($bopmRegistrosRaw)) ? '' : 'is-hidden' }}" id="bopmRegistrosWrap">
                  <div class="rep-bopm-registros__title">Registros do BOPM</div>
                  <div class="row g-3" id="bopmRegistrosContainer"></div>
                  <div class="rep-bopm-help">
                    Se informar quantidade de BOPM, será obrigatório preencher todos os registros correspondentes.
                  </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-black mb-3" style="color: var(--rep-text);">Observações</h6>
                <textarea
                  class="form-control"
                  name="observacoes"
                  rows="5"
                  placeholder="Observações do patrulhamento..."
                  style="min-height: 120px;"
                >{{ (string)($relatorio->observacoes ?? '') }}</textarea>
              </form>
            @else
              <h6 class="fw-black mb-3" style="color: var(--rep-text);">Apreensões</h6>
              <div class="row g-3">
                <div class="col-md-3"><div class="rep-field-k">Pistolas</div><div class="rep-field-v">{{ (int)($relatorio->pistolas ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">SMG / Fuzil</div><div class="rep-field-v">{{ (int)($relatorio->smg_fuzil ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Munições</div><div class="rep-field-v">{{ (int)($relatorio->municoes ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Drogas</div><div class="rep-field-v">{{ (int)($relatorio->drogas ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Explosivos</div><div class="rep-field-v">{{ (int)($relatorio->explosivos ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Lockpicks</div><div class="rep-field-v">{{ (int)($relatorio->lockpicks ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Dinheiro</div><div class="rep-field-v">R$ {{ number_format((int)($relatorio->dinheiro ?? 0), 0, ',', '.') }}</div></div>
              </div>

              <hr class="my-4">

              <h6 class="fw-black mb-3" style="color: var(--rep-text);">Multas / Ações</h6>
              <div class="row g-3">
                <div class="col-md-3"><div class="rep-field-k">Abordagens</div><div class="rep-field-v">{{ (int)($relatorio->abordagens ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Apoio</div><div class="rep-field-v">{{ (int)($relatorio->apoio ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Incursão</div><div class="rep-field-v">{{ (int)($relatorio->incursao ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Negociação</div><div class="rep-field-v">{{ (int)($relatorio->negociacao ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Blitz</div><div class="rep-field-v">{{ (int)($relatorio->blitz ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Escolta</div><div class="rep-field-v">{{ (int)($relatorio->escolta ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Multas</div><div class="rep-field-v">{{ (int)($relatorio->multas ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">BOPM</div><div class="rep-field-v">{{ (int)($relatorio->bopm ?? 0) }}</div></div>
                <div class="col-md-3"><div class="rep-field-k">Viaturas fiscalizadas</div><div class="rep-field-v">{{ (int)($relatorio->viaturas_fiscalizadas ?? 0) }}</div></div>
              </div>

              @if(count($bopmRegistrosRaw))
                <hr class="my-4">
                <h6 class="fw-black mb-3" style="color: var(--rep-text);">Registros do BOPM</h6>
                <div class="row g-3">
                  @foreach($bopmRegistrosRaw as $idx => $registro)
                    <div class="col-md-6">
                      <div class="rep-soft-box">
                        <div class="rep-field-k">Registro BOPM {{ $idx + 1 }}</div>
                        <div class="rep-field-v">{{ $registro !== '' ? $registro : '—' }}</div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif

              @if(!empty($relatorio->observacoes ?? null))
                <hr class="my-4">
                <h6 class="fw-black mb-3" style="color: var(--rep-text);">Observações</h6>
                <div class="rep-soft-box" style="white-space:pre-wrap;">{{ $relatorio->observacoes }}</div>
              @endif
            @endif
          </div>
        </details>

        @if(!$isEmPatrulha)
        <section class="rep-section">
          <div class="rep-section__head">
            <div>
              <div class="rep-section__title">Decisão administrativa</div>
              <div class="rep-section__sub">Aprovação ou reprovação do relatório com justificativa obrigatória.</div>
            </div>
            <div class="rep-section__sub">nível 6+</div>
          </div>

          <div class="rep-section__body">
            @if($canDecide)
              @if($canDecisionNow)
                <form method="POST" class="rep-form rep-decision-form">
                  @csrf

                  <div class="mb-3">
                    <label class="rep-field-k" style="display:block;">
                      Justificativa da decisão <span class="text-danger">*</span>
                    </label>

                    <textarea
                      name="observacao"
                      class="form-control rep-obs-input"
                      placeholder="Ex.: aprovado, relatório consistente e dentro do protocolo..."
                      required
                      minlength="10"
                      maxlength="400"
                      rows="4"
                    ></textarea>

                    <div class="rep-obs-meta">
                      <span>Obrigatório • mínimo 10 caracteres • máximo 400</span>
                      <span data-counter>0/400</span>
                    </div>

                    <div class="rep-note mt-2">
                      A decisão ficará registrada na auditoria interna do sistema.
                    </div>
                  </div>

                  <div class="d-flex gap-2 flex-wrap">
                    <button
                      class="btn btn-outline-success rep-btn"
                      type="submit"
                      formaction="{{ route('relatorios.aprovar', $relatorio) }}">
                      Aprovar relatório
                    </button>

                    <button
                      class="btn btn-outline-danger rep-btn"
                      type="submit"
                      formaction="{{ route('relatorios.reprovar', $relatorio) }}">
                      Reprovar relatório
                    </button>
                  </div>
                </form>
              @elseif(($relatorio->status ?? '') !== 'pendente')
                <div class="rep-soft-box">
                  <div class="rep-note" style="font-size:14px;">
                    Este relatório já possui uma decisão registrada.
                  </div>
                </div>
              @else
                <div class="rep-soft-box">
                  <div class="rep-note" style="font-size:14px;">
                    Aguardando encerramento do turno para liberar a decisão.
                  </div>
                </div>
              @endif
            @else
              @if(!auth()->check())
                <div class="rep-soft-box">
                  <div class="rep-note" style="font-size:14px;">
                    Faça login para acessar decisões administrativas.
                  </div>
                </div>
              @elseif($nivel < 6)
                <div class="rep-soft-box">
                  <div class="rep-note" style="font-size:14px;">
                    Acesso de decisão disponível apenas para nível 6 ou superior.
                  </div>
                </div>
              @elseif($blockedByGuarnicao)
                <div class="rep-soft-box">
                  <div class="rep-note" style="font-size:14px;">
                    Bloqueado: você está na guarnição deste relatório e não pode decidir.
                  </div>
                  <div class="rep-note mt-1">
                    Somente o Diretor (nível 10) pode decidir nesses casos.
                  </div>
                </div>
              @else
                <div class="rep-soft-box">
                  <div class="rep-note" style="font-size:14px;">
                    Sem permissão para decidir este relatório.
                  </div>
                </div>
              @endif
            @endif
          </div>
        </section>
        @endif

        @if(!$isEmPatrulha)
        <details class="rep-section">
          <summary class="rep-section__head">
            <div class="rep-summary__row">
              <span class="rep-section__title">Decisão e auditoria</span>
              <span class="rep-summary__chev">▾</span>
            </div>
          </summary>

          <div class="rep-section__body">
            @if(!empty($obsDecisao))
              <div class="grr-decision mb-3">
                <div class="grr-decision__title">Observação da decisão</div>
                <div class="grr-decision__box" style="white-space:pre-wrap;">{{ $obsDecisao }}</div>
              </div>
            @endif

            @if(($st === 'aprovado') && $aprovadoPor)
              <div class="grr-auditline mb-2">
                <span class="grr-auditline__k">Aprovado por:</span>
                <span class="grr-auditline__v">{!! $fmtById($apU, $aprovadoPor) !!}</span>
              </div>
            @endif

            @if(($st === 'reprovado') && $reprovadoPor)
              <div class="grr-auditline">
                <span class="grr-auditline__k">Reprovado por:</span>
                <span class="grr-auditline__v">{!! $fmtById($rpU, $reprovadoPor) !!}</span>
              </div>
            @endif

            @if(empty($obsDecisao) && empty($aprovadoPor) && empty($reprovadoPor))
              <div class="rep-note">Nenhuma informação de decisão registrada.</div>
            @endif
          </div>
        </details>
        @endif

      </div>
    </div>

    @php
      $p = $rows ?? $items ?? $logs ?? null;
    @endphp

    @if($p && method_exists($p, 'links'))
      @php
        $currentPage = (int) $p->currentPage();
        $lastPage    = (int) $p->lastPage();
        $from        = (int) ($p->firstItem() ?? 0);
        $to          = (int) ($p->lastItem() ?? 0);
        $total       = (int) ($p->total() ?? 0);
      @endphp

      <div class="grr-pager mt-3">
        <div class="grr-pager__left">
          <span class="grr-pager__dot"></span>
          <span class="grr-pager__txt">
            Página <b>{{ $currentPage }}</b> de <b>{{ $lastPage }}</b>
          </span>
        </div>

        <div class="grr-pager__right">
          <div class="grr-pager__meta">
            Showing <b>{{ $from }}</b> to <b>{{ $to }}</b> of <b>{{ $total }}</b> results
          </div>

          <div class="grr-pager__links">
            {{ $p->onEachSide(1)->links('pagination::bootstrap-5') }}
          </div>
        </div>
      </div>
    @endif

  </div>
</div>

<div id="turnoLoadingOverlay" class="turno-loading-overlay d-none" aria-live="assertive" aria-busy="true">
  <div class="turno-loading-card">
    <div class="turno-loading-spinner"></div>
    <div class="turno-loading-title" id="turnoLoadingTitle">Encerrando turno...</div>
    <div class="turno-loading-sub" id="turnoLoadingSub">
      Aguarde enquanto o sistema finaliza o relatório e envia para validação.
    </div>
  </div>
</div>

@if($editable)
<script>
  (function(){
    const form = document.getElementById('formRascunho');
    const stateEl = document.getElementById('autosaveState');
    if(!form) return;

    const url = @json(route('relatorios.rascunho', $relatorio));
    const csrf = @json(csrf_token());
    const bopmInitial = @json($bopmRegistrosRaw);

    let timer = null;
    let inflight = false;
    let dirty = false;
    let lastKey = null;

    function nowTime(){
      const d = new Date();
      const hh = String(d.getHours()).padStart(2,'0');
      const mm = String(d.getMinutes()).padStart(2,'0');
      const ss = String(d.getSeconds()).padStart(2,'0');
      return `${hh}:${mm}:${ss}`;
    }

    function setState(txt){
      if(stateEl) stateEl.textContent = txt;
    }

    function setTurnoLoadingText(title, sub){
      const titleEl = document.getElementById('turnoLoadingTitle');
      const subEl = document.getElementById('turnoLoadingSub');
      if(titleEl) titleEl.textContent = title || 'Processando...';
      if(subEl) subEl.textContent = sub || 'Aguarde enquanto o sistema conclui a operação.';
    }

    function activateTurnoLoading(button, title, sub, buttonText){
      const overlay = document.getElementById('turnoLoadingOverlay');

      setTurnoLoadingText(title, sub);

      if(button){
        button.dataset.submitting = '1';
        button.disabled = true;
        button.innerHTML = `<span class="turno-btn-spinner"></span><span>${buttonText || 'Processando...'}</span>`;
      }

      document.body.classList.add('turno-is-submitting');

      if(overlay){
        overlay.classList.remove('d-none');
      }
    }

    function getBopmCount(){
      const el = document.getElementById('inputBopm');
      if(!el) return 0;
      const n = parseInt((el.value || '0').toString(), 10);
      return Number.isFinite(n) && n > 0 ? n : 0;
    }

    function currentBopmValues(){
      return Array.from(document.querySelectorAll('input[name="bopm_registros[]"]'))
        .map(i => (i.value || '').trim());
    }

    function renderBopmRegistros(initialValues){
      const qtd = getBopmCount();
      const wrap = document.getElementById('bopmRegistrosWrap');
      const container = document.getElementById('bopmRegistrosContainer');
      if(!wrap || !container) return;

      const prev = Array.isArray(initialValues) ? initialValues : currentBopmValues();

      container.innerHTML = '';

      if(qtd <= 0){
        wrap.classList.add('is-hidden');
        return;
      }

      wrap.classList.remove('is-hidden');

      for(let i = 0; i < qtd; i++){
        const col = document.createElement('div');
        col.className = 'col-md-6';

        const value = (prev[i] ?? '').toString();

        col.innerHTML = `
          <div class="rep-bopm-registro-card">
            <label class="rep-field-k">Registro do BOPM ${i + 1}</label>
            <input
              type="text"
              class="form-control"
              name="bopm_registros[]"
              placeholder="Informe o número/registro do BOPM ${i + 1}"
              value="${value.replace(/"/g, '&quot;')}"
              ${qtd > 0 ? 'required' : ''}
            >
          </div>
        `;

        container.appendChild(col);
      }

      container.querySelectorAll('input[name="bopm_registros[]"]').forEach(el => {
        el.addEventListener('input', scheduleSave);
        el.addEventListener('change', scheduleSave);
      });
    }

    function validateBopmRegistros(showAlert = false){
      const qtd = getBopmCount();
      const values = currentBopmValues();

      if(qtd === 0) return true;

      const okLength = values.length === qtd;
      const okFilled = values.every(v => v !== '');

      if(okLength && okFilled) return true;

      if(showAlert){
        alert('Você informou BOPM, então é obrigatório preencher todos os registros do BOPM antes de encerrar o turno.');
      }

      return false;
    }

    function normalizePayload(raw){
      const ints = new Set([
        'pistolas','smg_fuzil','municoes','drogas','explosivos','lockpicks','dinheiro',
        'abordagens','apoio','incursao','negociacao','blitz','escolta',
        'multas','bopm','viaturas_fiscalizadas'
      ]);

      const out = {};

      for(const k in raw){
        if(!Object.prototype.hasOwnProperty.call(raw, k)) continue;

        let v = raw[k];

        if(ints.has(k)){
          v = (v ?? '').toString().trim();
          if(v === '') out[k] = null;
          else out[k] = Number.isFinite(parseInt(v,10)) ? parseInt(v,10) : null;
          continue;
        }

        if(k === 'observacoes'){
          out[k] = (v ?? '').toString();
          continue;
        }

        if(k === 'bopm_registros'){
          out[k] = Array.isArray(v)
            ? v.map(x => (x ?? '').toString().trim()).filter(x => x !== '')
            : [];
        }
      }

      return out;
    }

    function getPayload(){
      const fd = new FormData(form);
      const obj = {};

      for(const [k,v] of fd.entries()){
        if(k === 'bopm_registros[]'){
          if(!Array.isArray(obj.bopm_registros)) obj.bopm_registros = [];
          obj.bopm_registros.push(v);
        } else {
          obj[k] = v;
        }
      }

      return normalizePayload(obj);
    }

    async function saveNow(force=false){
      const payload = getPayload();
      const key = JSON.stringify(payload);

      if(!force && lastKey === key) return true;
      if(inflight) return false;

      inflight = true;
      dirty = false;
      setState('salvando...');

      try{
        const res = await fetch(url, {
          method: 'PUT',
          credentials: 'same-origin',
          headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        });

        if(res.status === 419){
          setState('erro (CSRF/419)');
          inflight = false;
          return false;
        }

        if(res.status === 422){
          setState('erro (validação)');
          inflight = false;
          return false;
        }

        if(!res.ok){
          setState('erro (' + res.status + ')');
          inflight = false;
          return false;
        }

        lastKey = key;
        setState('salvo ' + nowTime());
        return true;
      }catch(e){
        setState('erro (rede)');
        return false;
      }finally{
        inflight = false;
      }
    }

    function scheduleSave(){
      dirty = true;
      setState('editando...');
      if(timer) clearTimeout(timer);
      timer = setTimeout(() => saveNow(false), 650);
    }

    form.querySelectorAll('input, textarea').forEach(el => {
      el.addEventListener('input', scheduleSave);
      el.addEventListener('change', scheduleSave);
    });

    const inputBopm = document.getElementById('inputBopm');
    if(inputBopm){
      inputBopm.addEventListener('input', function(){
        renderBopmRegistros();
        scheduleSave();
      });

      inputBopm.addEventListener('change', function(){
        renderBopmRegistros();
        scheduleSave();
      });
    }

    renderBopmRegistros(bopmInitial);

    const encerrarForm = document.getElementById('formEncerrarTurno');
    if(encerrarForm){
      encerrarForm.addEventListener('submit', async function(e){
        const btn = document.getElementById('btnEncerrarTurno');
        const confirmMsg =
`Deseja encerrar o relatório?

Verifique todos os campos antes de enviar para ver se tudo estão certos.

Ao fazer o envio de maneira errada o relatório não poderá nunca/jamais ser editado!`;

        if(btn && btn.dataset.submitting === '1'){
          e.preventDefault();
          return;
        }

        if(!validateBopmRegistros(true)){
          e.preventDefault();
          return;
        }

        if(!window.confirm(confirmMsg)){
          e.preventDefault();
          return;
        }

        e.preventDefault();

        let saveOk = true;
        if(dirty){
          saveOk = await saveNow(true);
        }

        if(!saveOk){
          alert('Não foi possível salvar os dados antes de encerrar. Tente novamente.');
          return;
        }

        if(!validateBopmRegistros(true)) return;

        activateTurnoLoading(
          btn,
          'Encerrando turno...',
          'Aguarde enquanto o sistema finaliza o relatório e envia para validação.',
          'Encerrando turno...'
        );

        encerrarForm.submit();
      });
    }

    window.addEventListener('beforeunload', function(){
      if(!dirty) return;
      try{
        const payload = getPayload();
        const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
        navigator.sendBeacon(url, blob);
      }catch(e){}
    });

    setState('pronto');
  })();
</script>
@endif

@if($canForceClose && !$editable)
<script>
  (function(){
    const form = document.getElementById('formEncerrarTurno');
    const btn  = document.getElementById('btnEncerrarTurno');
    if(!form || !btn) return;

    function setTurnoLoadingText(title, sub){
      const titleEl = document.getElementById('turnoLoadingTitle');
      const subEl = document.getElementById('turnoLoadingSub');
      if(titleEl) titleEl.textContent = title || 'Processando...';
      if(subEl) subEl.textContent = sub || 'Aguarde enquanto o sistema conclui a operação.';
    }

    function activateTurnoLoading(){
      const overlay = document.getElementById('turnoLoadingOverlay');

      setTurnoLoadingText(
        'Fechando patrulha...',
        'Aguarde enquanto o sistema finaliza esta patrulha e atualiza o status do relatório.'
      );

      btn.dataset.submitting = '1';
      btn.disabled = true;
      btn.innerHTML = '<span class="turno-btn-spinner"></span><span>Fechando patrulha...</span>';

      document.body.classList.add('turno-is-submitting');

      if(overlay){
        overlay.classList.remove('d-none');
      }
    }

    form.addEventListener('submit', function(e){
      const confirmMsg =
`Deseja encerrar o relatório?

Verifique todos os campos antes de enviar para ver se tudo estão certos.

Ao fazer o envio de maneira errada o relatório não poderá nunca/jamais ser editado!`;

      if(btn.dataset.submitting === '1'){
        e.preventDefault();
        return;
      }

      if(!window.confirm(confirmMsg)){
        e.preventDefault();
        return;
      }

      activateTurnoLoading();
    });
  })();
</script>
@endif

<script>
  function bindDecisionForm(form){
    const ta = form.querySelector('.rep-obs-input');
    const counter = form.querySelector('[data-counter]');
    if(!ta) return;

    const max = parseInt(ta.getAttribute('maxlength') || '400', 10);
    const min = parseInt(ta.getAttribute('minlength') || '10', 10);

    function update(){
      const len = (ta.value || '').trim().length;
      if(counter) counter.textContent = `${Math.min(len, max)}/${max}`;

      if(len > 0 && len < min){
        ta.classList.add('is-invalid');
      } else {
        ta.classList.remove('is-invalid');
      }
    }

    ta.addEventListener('input', update);
    update();

    form.addEventListener('submit', function(e){
      const len = (ta.value || '').trim().length;
      if(len < min){
        e.preventDefault();
        ta.focus();
        update();
      }
    });
  }

  document.querySelectorAll('.rep-decision-form').forEach(bindDecisionForm);
</script>
@endsection