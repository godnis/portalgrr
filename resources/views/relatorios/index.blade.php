@extends('layouts.app')

@section('content')
<div class="rep-wrap">

  @php
    $backUrl = request()->fullUrl();
    $backEncoded = urlencode($backUrl);

    $hasPaginator = is_object($relatorios) && method_exists($relatorios, 'links');
    $totalAll = $hasPaginator && method_exists($relatorios, 'total')
      ? (int)$relatorios->total()
      : (is_countable($relatorios) ? count($relatorios) : 0);

    $from = ($hasPaginator && method_exists($relatorios, 'firstItem'))
      ? ($relatorios->firstItem() ?? 0)
      : ($totalAll ? 1 : 0);

    $to = ($hasPaginator && method_exists($relatorios, 'lastItem'))
      ? ($relatorios->lastItem() ?? 0)
      : $totalAll;

    $auth = auth()->check();
    $rgAuth = $auth ? preg_replace('/\D+/', '', (string)(auth()->user()->rg ?? '')) : '';

    $unidades = [
      'GRR-01 CMD',
      'GRR-02 CRD',
      'GRR-03 SUP-A',
      'GRR-04 SUP-B',
      'GRR-05',
      'GRR-06',
      'GRR-10',
      'GRR-11',
      'GRR-15',
      'GRR-16',
      'GRR-17',
      'GRR-18',
      'GRR-22063',
      'GRR-511',
      'Batedor 01',
      'Batedor 02',
      'Batedor 03',
      'Administrativo',
      'Blitz',
      'Guincho',
      'DEJEM',
    ];

    $oldUnidade = (string) old('unidade', '');
    $unidadeIsOther = $oldUnidade !== '' && !in_array($oldUnidade, $unidades, true);

    $statsTotal = $totalAll;
    $statsPatrulha = 0;
    $statsPendente = 0;
    $statsAprovado = 0;
    $statsReprovado = 0;

    $statsSource = $hasPaginator ? $relatorios->getCollection() : collect($relatorios);

    foreach ($statsSource as $r) {
      $st = (string)($r->status ?? 'pendente');
      if ($st === 'em_patrulha') $statsPatrulha++;
      elseif ($st === 'aprovado') $statsAprovado++;
      elseif ($st === 'reprovado') $statsReprovado++;
      else $statsPendente++;
    }
  @endphp

  <style>
    .rep-wrap{
      padding: 8px 0 18px;

      --rep-radius-2xl: 26px;
      --rep-radius-xl: 22px;
      --rep-radius-lg: 18px;
      --rep-radius-md: 14px;
      --rep-radius-sm: 12px;

      --rep-bg: #f8fafc;
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
      --rep-hover: rgba(37,99,235,.05);
      --rep-head: #f1f5f9;
    }

    html[data-theme="dark"] .rep-wrap{
      --rep-bg: rgba(0,0,0,.35);
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
      --rep-hover: rgba(90,162,255,.08);
      --rep-head: rgba(148,163,184,.10);
    }

    .rep-alert{
      border-radius: 16px;
      border: 1px solid var(--rep-border);
      box-shadow: var(--rep-shadow-soft);
    }

    .adm-hidden{ display:none !important; }

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
      display: grid;
      grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr);
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

    html[data-theme="dark"] .rep-badge__dot{
      box-shadow: 0 0 0 4px rgba(90,162,255,.14);
    }

    .rep-actions .rep-btn{
      border-radius: 14px;
      font-weight: 900;
      padding: 11px 16px;
      white-space: nowrap;
      box-shadow: var(--rep-shadow-soft);
    }

    .rep-actions .btn-primary{
      background: linear-gradient(135deg, var(--rep-primary), var(--rep-primary-2));
      border-color: transparent;
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

    .rep-stats{
      display:grid;
      grid-template-columns: repeat(4, minmax(0,1fr));
      gap: 12px;
      margin-bottom: 16px;
    }

    @media (max-width: 992px){
      .rep-stats{ grid-template-columns: repeat(2, minmax(0,1fr)); }
    }

    @media (max-width: 576px){
      .rep-stats{ grid-template-columns: 1fr; }
    }

    .rep-stat{
      position: relative;
      overflow: hidden;
      border-radius: 18px;
      border: 1px solid var(--rep-border);
      background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.96));
      box-shadow: var(--rep-shadow-soft);
      padding: 16px;
      min-height: 112px;
    }

    html[data-theme="dark"] .rep-stat{
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.04));
    }

    .rep-stat::after{
      content: "";
      position: absolute;
      right: -20px;
      top: -20px;
      width: 88px;
      height: 88px;
      border-radius: 50%;
      background: rgba(37,99,235,.06);
    }

    .rep-stat--success::after{ background: rgba(16,185,129,.09); }
    .rep-stat--warning::after{ background: rgba(245,158,11,.09); }
    .rep-stat--danger::after{ background: rgba(239,68,68,.08); }

    .rep-stat__top{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      position: relative;
      z-index: 1;
    }

    .rep-stat__label{
      font-size: 12px;
      font-weight: 900;
      letter-spacing: .06em;
      text-transform: uppercase;
      color: var(--rep-muted);
    }

    .rep-stat__icon{
      width: 38px;
      height: 38px;
      border-radius: 12px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size: 16px;
      font-weight: 900;
      color: var(--rep-text);
      border: 1px solid var(--rep-border);
      background: rgba(255,255,255,.72);
    }

    html[data-theme="dark"] .rep-stat__icon{
      background: rgba(255,255,255,.06);
    }

    .rep-stat__value{
      margin-top: 14px;
      position: relative;
      z-index: 1;
      font-size: 30px;
      line-height: 1;
      font-weight: 950;
      letter-spacing: -.04em;
      color: var(--rep-text);
    }

    .rep-stat__sub{
      margin-top: 6px;
      position: relative;
      z-index: 1;
      font-size: 12px;
      font-weight: 700;
      color: var(--rep-muted);
    }

    .rep-card{
      border: 1px solid var(--rep-border);
      background: var(--rep-card);
      border-radius: var(--rep-radius-2xl);
      box-shadow: var(--rep-shadow);
      overflow: hidden;
    }

    .rep-card__head{
      padding: 16px;
      display:grid;
      grid-template-columns: minmax(0,1fr) minmax(320px, 520px);
      gap: 16px;
      align-items: start;
      border-bottom: 1px solid var(--rep-border);
      background:
        linear-gradient(180deg, rgba(148,163,184,.06), rgba(148,163,184,.03));
    }

    @media (max-width: 992px){
      .rep-card__head{ grid-template-columns: 1fr; }
    }

    .rep-card__title{
      font-size: 19px;
      line-height: 1.2;
      font-weight: 950;
      letter-spacing: -.03em;
      color: var(--rep-text);
    }

    .rep-card__sub{
      margin-top: 4px;
      font-size: 13px;
      font-weight: 700;
      color: var(--rep-muted);
      line-height: 1.5;
    }

    .rep-card__meta{
      margin-top: 8px;
      font-size: 12px;
      color: var(--rep-muted);
      font-weight: 800;
    }

    .rep-toolbar{
      border: 1px solid var(--rep-border);
      border-radius: 18px;
      padding: 12px;
      background: rgba(255,255,255,.68);
      box-shadow: var(--rep-shadow-soft);
    }

    html[data-theme="dark"] .rep-toolbar{
      background: rgba(255,255,255,.04);
    }

    .rep-filter-form{
      display:grid;
      grid-template-columns: minmax(0,1fr) 210px auto auto;
      gap: 10px;
      align-items:center;
    }

    @media (max-width: 992px){
      .rep-filter-form{
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 576px){
      .rep-filter-form{
        grid-template-columns: 1fr;
      }
    }

    .rep-toolbar .form-control,
    .rep-toolbar .form-select{
      border-radius: 14px !important;
      min-height: 46px;
      border: 1px solid var(--rep-border) !important;
      background: rgba(255,255,255,.90) !important;
      color: var(--rep-text) !important;
      font-weight: 700;
      box-shadow: none !important;
    }

    html[data-theme="dark"] .rep-toolbar .form-control,
    html[data-theme="dark"] .rep-toolbar .form-select{
      background: rgba(14,19,28,.92) !important;
      border-color: rgba(255,255,255,.12) !important;
    }

    .rep-toolbar .form-control::placeholder{
      color: var(--rep-muted-2);
    }

    .rep-toolbar .form-control:focus,
    .rep-toolbar .form-select:focus{
      border-color: rgba(37,99,235,.45) !important;
      box-shadow: 0 0 0 4px rgba(37,99,235,.10) !important;
    }

    html[data-theme="dark"] .rep-toolbar .form-control:focus,
    html[data-theme="dark"] .rep-toolbar .form-select:focus{
      border-color: rgba(90,162,255,.55) !important;
      box-shadow: 0 0 0 4px rgba(90,162,255,.16) !important;
    }

    .rep-toolbar .btn{
      border-radius: 14px;
      min-height: 46px;
      font-weight: 900;
      padding-inline: 16px;
      white-space: nowrap;
    }

    .rep-table-shell{
      padding: 0 12px 12px;
    }

    .rep-table-wrap{
      margin-top: 12px;
      border: 1px solid var(--rep-border);
      border-radius: 18px;
      overflow: hidden;
      background: var(--rep-card-2);
    }

    html[data-theme="dark"] .rep-table-wrap{
      background: rgba(0,0,0,.30);
    }

    .rep-table{
      margin:0;
      width: 100%;
      table-layout: fixed;
      --bs-table-bg: transparent !important;
      --bs-table-accent-bg: transparent !important;
      --bs-table-striped-bg: transparent !important;
      --bs-table-hover-bg: transparent !important;
      color: var(--rep-text) !important;
    }

    .rep-table > :not(caption) > * > *{
      background-color: transparent !important;
      box-shadow: none !important;
      vertical-align: middle;
    }

    .rep-table thead th{
      font-size: 11px;
      font-weight: 950;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--rep-muted);
      background: var(--rep-head) !important;
      border-bottom: 1px solid var(--rep-border) !important;
      padding-top: 14px;
      padding-bottom: 14px;
      white-space: nowrap;
    }

    .rep-table td{
      border-top: 1px solid var(--rep-border-2) !important;
      white-space: nowrap;
      color: var(--rep-text) !important;
      font-variant-numeric: tabular-nums;
      padding-top: 14px;
      padding-bottom: 14px;
    }

    .rep-table tbody tr{
      transition: background .14s ease;
    }

    .rep-table tbody tr:hover{
      background: var(--rep-hover) !important;
    }

    .rep-table tr.is-ok{ background: rgba(16,185,129,.035); }
    .rep-table tr.is-bad{ background: rgba(239,68,68,.03); }
    .rep-table tr.is-live{ background: rgba(59,130,246,.035); }
    .rep-table tr.is-pending{ background: rgba(245,158,11,.03); }

    .rep-id__pill{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding: 7px 11px;
      min-width: 64px;
      border-radius: 999px;
      border: 1px solid var(--rep-border);
      background: rgba(255,255,255,.72);
      font-weight: 950;
      color: var(--rep-text);
    }

    html[data-theme="dark"] .rep-id__pill{
      background: rgba(255,255,255,.06);
    }

    .rep-mono{
      font-variant-numeric: tabular-nums;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
    }

    .rep-unit__top{
      font-weight: 950;
      color: var(--rep-text);
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .rep-live{
      display:inline-flex;
      align-items:center;
      gap:8px;
      font-weight: 900;
      color: var(--rep-text);
    }

    .rep-live__dot{
      width:8px;
      height:8px;
      border-radius:50%;
      background: var(--rep-success);
      box-shadow: 0 0 0 4px rgba(16,185,129,.14);
    }

    .rep-table .text-muted,
    .rep-table .rep-aux.text-muted{
      color: var(--rep-muted) !important;
    }

    .rep-status-cell{
      white-space: normal !important;
      min-width: 320px;
      max-width: 420px;
    }

    .rep-status{
      display:flex;
      flex-direction:column;
      gap:7px;
      min-width: 0;
      width: 100%;
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
      max-width: 100%;
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

    .rep-badge--ok{
      background: rgba(16,185,129,.12) !important;
      color: #047857 !important;
      border-color: rgba(16,185,129,.22);
    }

    .rep-badge--bad{
      background: rgba(239,68,68,.10) !important;
      color: #b91c1c !important;
      border-color: rgba(239,68,68,.18);
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

    .rep-aux{
      font-size:12px;
      line-height:1.45;
    }

    .rep-aux--tight{ margin-top:4px; }

    .rep-ellipsis{
      display: block;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .rep-actions-cell{
      width: 140px;
      min-width: 140px;
      max-width: 140px;
      white-space: nowrap !important;
    }

    .rep-actions-top{
      display:flex;
      gap:8px;
      flex-wrap:wrap;
      align-items:center;
      justify-content:flex-start;
    }

    .rep-action-btn{
      border-radius: 12px;
      font-weight: 900;
      padding: 8px 12px;
      white-space: nowrap;
    }

    .rep-inline{ display:inline-block; }

    .rep-pagination{
      padding: 14px 18px 8px;
      display:flex;
      flex-wrap:wrap;
      align-items:center;
      justify-content:space-between;
      gap: 14px;
      border-top: 1px solid var(--rep-border);
    }

    .rep-pagination__meta{
      font-size: 13px;
      color: var(--rep-muted);
      font-weight: 700;
      white-space: nowrap;
    }

    .rep-pagination .pagination{
      margin: 0;
      gap: 6px;
      flex-wrap: wrap;
    }

    .rep-pagination .page-item{
      display:flex;
    }

    .rep-pagination .page-link{
      min-width: 42px;
      height: 42px;
      padding: 0 12px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border-radius: 12px !important;
      border: 1px solid rgba(15,23,42,.10);
      background: rgba(255,255,255,.75);
      color: var(--rep-text);
      font-weight: 800;
      font-size: 14px;
      line-height: 1;
      box-shadow: none !important;
      transition: all .18s ease;
    }

    .rep-pagination .page-link:hover{
      background: rgba(37,99,235,.10);
      border-color: rgba(37,99,235,.24);
      color: var(--rep-primary);
      transform: translateY(-1px);
    }

    .rep-pagination .page-item.active .page-link{
      background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
      border-color: transparent !important;
      color: #fff !important;
      box-shadow: 0 10px 24px rgba(37,99,235,.28) !important;
    }

    .rep-pagination .page-item.disabled .page-link{
      background: rgba(148,163,184,.10);
      color: rgba(100,116,139,.70);
      border-color: rgba(148,163,184,.10);
      opacity: 1;
      cursor: not-allowed;
    }

    .rep-pagination .page-item:first-child .page-link,
    .rep-pagination .page-item:last-child .page-link{
      min-width: 42px;
      padding: 0 14px;
      font-size: 16px;
    }

    html[data-theme="dark"] .rep-pagination{
      border-top-color: rgba(255,255,255,.08);
    }

    html[data-theme="dark"] .rep-pagination__meta{
      color: rgba(231,237,246,.72);
    }

    html[data-theme="dark"] .rep-pagination .page-link{
      border: 1px solid rgba(255,255,255,.08);
      background: rgba(255,255,255,.03);
      color: rgba(231,237,246,.86);
    }

    html[data-theme="dark"] .rep-pagination .page-link:hover{
      background: rgba(90,162,255,.10);
      border-color: rgba(90,162,255,.24);
      color: #fff;
    }

    html[data-theme="dark"] .rep-pagination .page-item.disabled .page-link{
      background: rgba(255,255,255,.02);
      color: rgba(231,237,246,.34);
      border-color: rgba(255,255,255,.05);
    }

    .rep-foot{
      padding: 13px 16px;
      display:flex;
      justify-content:space-between;
      gap:10px;
      flex-wrap:wrap;
      border-top: 1px solid var(--rep-border);
      background: rgba(148,163,184,.05);
      font-size:12px;
      font-weight:800;
      color: var(--rep-muted);
    }

    .rep-foot__dot{
      display:inline-block;
      width:8px;
      height:8px;
      border-radius:50%;
      background: var(--rep-success);
      box-shadow: 0 0 0 4px rgba(16,185,129,.14);
      margin-right:8px;
      transform: translateY(1px);
    }

    #modalIniciarTurno .modal-content{
      border-radius: 24px;
      overflow: hidden;
      border: 1px solid rgba(255,255,255,.10);
      box-shadow: 0 28px 80px rgba(0,0,0,.55);
      background: linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,.98));
    }

    html[data-theme="dark"] #modalIniciarTurno .modal-content{
      background:
        linear-gradient(180deg, rgba(11,18,32,.98), rgba(8,14,26,.98));
      border-color: rgba(255,255,255,.12);
    }

    #modalIniciarTurno .modal-header{
      padding: 18px 20px;
      border-bottom: 1px solid var(--rep-border);
      background:
        radial-gradient(circle at top right, rgba(37,99,235,.10), transparent 28%),
        linear-gradient(180deg, rgba(148,163,184,.05), rgba(255,255,255,0));
    }

    html[data-theme="dark"] #modalIniciarTurno .modal-header{
      border-bottom-color: rgba(255,255,255,.10);
      background:
        radial-gradient(circle at top right, rgba(90,162,255,.14), transparent 28%),
        linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,0));
    }

    #modalIniciarTurno .modal-title{
      font-weight: 950;
      font-size: 1.2rem;
      letter-spacing: -.02em;
      color: var(--rep-text);
    }

    html[data-theme="dark"] #modalIniciarTurno .modal-title{
      color: #f8fbff;
    }

    #modalIniciarTurno .btn-close{
      opacity: .85;
    }

    html[data-theme="dark"] #modalIniciarTurno .btn-close{
      filter: invert(1) grayscale(100%);
    }

    #modalIniciarTurno .btn-close:hover{
      opacity: 1;
    }

    #modalIniciarTurno .modal-body{
      padding: 20px;
    }

    #modalIniciarTurno .modal-footer{
      padding: 16px 20px 20px;
      border-top: 1px solid var(--rep-border);
      background: rgba(148,163,184,.03);
    }

    html[data-theme="dark"] #modalIniciarTurno .modal-footer{
      border-top-color: rgba(255,255,255,.10);
      background: rgba(255,255,255,.02);
    }

    #modalIniciarTurno .form-label{
      color: var(--rep-text);
      font-weight: 900;
      margin-bottom: 7px;
      font-size: .95rem;
    }

    html[data-theme="dark"] #modalIniciarTurno .form-label{
      color: #f3f7fd;
    }

    #modalIniciarTurno .text-danger{
      color: #ff7b7b !important;
    }

    #modalIniciarTurno .form-control,
    #modalIniciarTurno .form-select{
      min-height: 50px;
      border-radius: 14px !important;
      border: 1px solid var(--rep-border) !important;
      background: rgba(255,255,255,.96) !important;
      color: var(--rep-text) !important;
      font-weight: 800;
      box-shadow: none !important;
    }

    html[data-theme="dark"] #modalIniciarTurno .form-control,
    html[data-theme="dark"] #modalIniciarTurno .form-select{
      background: rgba(255,255,255,.10) !important;
      border-color: rgba(255,255,255,.18) !important;
      color: #f8fbff !important;
    }

    #modalIniciarTurno .form-control::placeholder{
      color: var(--rep-muted-2) !important;
      font-weight: 700;
    }

    html[data-theme="dark"] #modalIniciarTurno .form-control::placeholder{
      color: rgba(232,238,248,.68) !important;
    }

    #modalIniciarTurno .form-select{
      color: var(--rep-text) !important;
    }

    html[data-theme="dark"] #modalIniciarTurno .form-select{
      color: #f8fbff !important;
    }

    html[data-theme="dark"] #modalIniciarTurno .form-select option{
      background: #0f172a;
      color: #f8fbff;
    }

    #modalIniciarTurno .form-control[readonly]{
      opacity: 1 !important;
    }

    html[data-theme="dark"] #modalIniciarTurno .form-control[readonly]{
      background: rgba(255,255,255,.08) !important;
      color: #ffffff !important;
    }

    #modalIniciarTurno .form-control:focus,
    #modalIniciarTurno .form-select:focus{
      border-color: rgba(37,99,235,.45) !important;
      box-shadow: 0 0 0 4px rgba(37,99,235,.10) !important;
    }

    html[data-theme="dark"] #modalIniciarTurno .form-control:focus,
    html[data-theme="dark"] #modalIniciarTurno .form-select:focus{
      border-color: rgba(90,162,255,.70) !important;
      box-shadow: 0 0 0 4px rgba(90,162,255,.18) !important;
      background: rgba(255,255,255,.12) !important;
    }

    #modalIniciarTurno .small,
    #modalIniciarTurno .text-muted{
      color: var(--rep-muted) !important;
      font-weight: 700;
    }

    html[data-theme="dark"] #modalIniciarTurno .small,
    html[data-theme="dark"] #modalIniciarTurno .text-muted{
      color: rgba(226,234,245,.80) !important;
    }

    #modalIniciarTurno [id^="rgInfo"]{
      min-height: 18px;
      font-size: 12px;
      line-height: 1.45;
    }

    #modalIniciarTurno .text-success{
      color: #10b981 !important;
      font-weight: 800 !important;
    }

    html[data-theme="dark"] #modalIniciarTurno .text-success{
      color: #34d399 !important;
    }

    #modalIniciarTurno .text-danger{
      color: #dc2626 !important;
      font-weight: 800 !important;
    }

    html[data-theme="dark"] #modalIniciarTurno .text-danger{
      color: #f87171 !important;
    }

    #modalIniciarTurno .alert{
      border-radius: 18px;
      border: 1px solid rgba(15,23,42,.08);
    }

    #modalIniciarTurno .alert-light{
      background: rgba(255,255,255,.94) !important;
      color: #334155 !important;
      border-color: rgba(15,23,42,.10) !important;
    }

    html[data-theme="dark"] #modalIniciarTurno .alert-light{
      background: rgba(255,255,255,.92) !important;
      color: #334155 !important;
      border-color: rgba(255,255,255,.22) !important;
    }

    #modalIniciarTurno .btn{
      min-height: 46px;
      border-radius: 14px;
      font-weight: 900;
      padding-inline: 16px;
    }

    #modalIniciarTurno .btn-primary{
      background: linear-gradient(135deg, #5aa2ff, #3b82f6);
      border-color: transparent;
      color: #06101f;
    }

    #modalIniciarTurno .btn-outline-secondary{
      border-color: rgba(148,163,184,.35);
      color: var(--rep-text);
    }

    html[data-theme="dark"] #modalIniciarTurno .btn-outline-secondary{
      border-color: rgba(255,255,255,.22);
      color: #e8eef8;
    }

    #modalIniciarTurno .btn-outline-secondary:hover{
      background: rgba(148,163,184,.08);
    }

    html[data-theme="dark"] #modalIniciarTurno .btn-outline-secondary:hover{
      background: rgba(255,255,255,.08);
      color: #ffffff;
      border-color: rgba(255,255,255,.30);
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
      border: 2px solid rgba(6,16,31,.25);
      border-top-color: rgba(6,16,31,1);
      border-radius: 50%;
      display: inline-block;
      animation: turnoSpin .8s linear infinite;
      vertical-align: -3px;
      margin-right: 8px;
    }

    html[data-theme="dark"] .turno-btn-spinner{
      border-color: rgba(255,255,255,.25);
      border-top-color: rgba(255,255,255,1);
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

    @media (max-width: 768px){
      #modalIniciarTurno .modal-body{
        padding: 16px;
      }

      #modalIniciarTurno .modal-footer{
        padding: 14px 16px 16px;
      }

      #modalIniciarTurno .btn{
        width: 100%;
      }

      .rep-title{ font-size: 25px; }
      .rep-actions .rep-btn{ width: 100%; justify-content: center; }
      .rep-table th, .rep-table td{ font-size: 13px; }

      .rep-pagination{
        flex-direction: column;
        align-items: flex-start;
      }

      .rep-pagination__meta{
        white-space: normal;
      }

      .rep-pagination .page-link{
        min-width: 38px;
        height: 38px;
        border-radius: 10px !important;
        font-size: 13px;
      }

      .rep-status-cell{
        min-width: 260px;
        max-width: 260px;
      }

      .rep-actions-cell{
        width: 120px;
        min-width: 120px;
        max-width: 120px;
      }
    }
  </style>

  <div class="rep-hero">
    <div class="rep-hero__bg"></div>

    <div class="rep-hero__content">
      <div>
        <div class="rep-kicker">GRR • PRF — Central Operacional</div>
        <h1 class="rep-title">Relatórios de Patrulhamento</h1>
        <div class="rep-sub">
          Painel de controle para iniciar turno, acompanhar relatórios, validar registros operacionais e registrar decisões com rastreabilidade e auditoria interna.
        </div>

        <div class="rep-badges">
          <span class="rep-badge">
            <span class="rep-badge__dot"></span>
            auditoria ativa
          </span>

          <span class="rep-badge rep-badge--soft">
            decisões no relatório
          </span>
        </div>
      </div>

      <div>
        <div class="rep-actions d-flex gap-2 flex-wrap">
          <button type="button" class="btn btn-primary rep-btn" data-bs-toggle="modal" data-bs-target="#modalIniciarTurno">
            Iniciar turno
          </button>

          <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rep-btn">
            Ver dashboard
          </a>
        </div>

        <div class="rep-mini mt-3">
          <div class="rep-mini__item">
            <div class="rep-mini__k">Fluxo operacional</div>
            <div class="rep-mini__v">Início do turno → preenchimento → auto-save → encerramento → decisão no relatório.</div>
          </div>

          <div class="rep-mini__item">
            <div class="rep-mini__k">Confiabilidade do sistema</div>
            <div class="rep-mini__v">Horários vinculados ao relógio do site para controle interno e prevenção de inconsistências.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="rep-stats">
    <div class="rep-stat">
      <div class="rep-stat__top">
        <div class="rep-stat__label">Total listados</div>
        <div class="rep-stat__icon">#</div>
      </div>
      <div class="rep-stat__value">{{ $statsTotal }}</div>
      <div class="rep-stat__sub">Quantidade atual de relatórios exibidos.</div>
    </div>

    <div class="rep-stat rep-stat--warning">
      <div class="rep-stat__top">
        <div class="rep-stat__label">Em patrulha / pendentes</div>
        <div class="rep-stat__icon">⏱</div>
      </div>
      <div class="rep-stat__value">{{ $statsPatrulha + $statsPendente }}</div>
      <div class="rep-stat__sub">Relatórios que ainda exigem andamento ou decisão.</div>
    </div>

    <div class="rep-stat rep-stat--success">
      <div class="rep-stat__top">
        <div class="rep-stat__label">Aprovados</div>
        <div class="rep-stat__icon">✓</div>
      </div>
      <div class="rep-stat__value">{{ $statsAprovado }}</div>
      <div class="rep-stat__sub">Registros validados pela administração.</div>
    </div>

    <div class="rep-stat rep-stat--danger">
      <div class="rep-stat__top">
        <div class="rep-stat__label">Reprovados</div>
        <div class="rep-stat__icon">!</div>
      </div>
      <div class="rep-stat__value">{{ $statsReprovado }}</div>
      <div class="rep-stat__sub">Relatórios com inconsistência ou reprovação.</div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success rep-alert"><b>Sucesso:</b> {{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger rep-alert"><b>Atenção:</b> {{ session('error') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger rep-alert">
      <b>Corrija os campos abaixo:</b>
      <ul class="mb-0 mt-2">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(session('clear_relatorio_draft'))
    <script>
      try { localStorage.removeItem('relatorio_create_draft_v1'); } catch (e) {}
      try { localStorage.removeItem('relatorio_client_token_v1'); } catch (e) {}
      try { localStorage.removeItem('relatorio_pending_submit_v1'); } catch (e) {}
    </script>
  @endif

  <div class="rep-card">
    <div class="rep-card__head">
      <div>
        <div class="rep-card__title">Lista de Relatórios</div>
        <div class="rep-card__sub">
          Painel administrativo para revisão, acompanhamento e acesso ao relatório completo.
        </div>

        @if($totalAll > 0)
          <div class="rep-card__meta">
            Mostrando <b>{{ $from }}</b> a <b>{{ $to }}</b> de <b>{{ $totalAll }}</b> resultados
          </div>
        @endif
      </div>

      <div class="rep-toolbar">
        <form method="GET" action="{{ route('relatorios.index') }}" class="rep-filter-form">
          <input
            type="text"
            name="rg"
            value="{{ request('rg') }}"
            class="form-control"
            placeholder="Buscar RG (motorista / P3 / P4 / P5 / chefe)"
            inputmode="numeric"
          >

          <select name="status" class="form-select">
            <option value="">Status (todos)</option>
            <option value="em_patrulha" @selected(request('status')==='em_patrulha')>Unidade em patrulha</option>
            <option value="pendente" @selected(request('status')==='pendente')>Pendente</option>
            <option value="aprovado" @selected(request('status')==='aprovado')>Aprovado</option>
            <option value="reprovado" @selected(request('status')==='reprovado')>Reprovado</option>
          </select>

          <button type="submit" class="btn btn-primary">Filtrar</button>
          <a href="{{ route('relatorios.index') }}" class="btn btn-outline-secondary">Limpar</a>
        </form>
      </div>
    </div>

    <div class="rep-table-shell">
      <div class="table-responsive rep-table-wrap">
        <table class="table table-sm align-middle rep-table">
          <thead>
            <tr>
              <th style="width: 84px;">ID</th>
              <th style="width: 120px;">Data</th>
              <th style="width: 84px;">Início</th>
              <th style="width: 120px;">Final</th>
              <th style="width: 110px;">Unidade</th>
              <th style="width: 220px;">Chefe (RG)</th>
              <th style="width: 420px;">Status</th>
              <th style="width: 140px;">Ações</th>
            </tr>
          </thead>

          <tbody>
            @forelse(($hasPaginator ? $relatorios : $relatorios) as $relatorio)
              @php
                $status = (string)($relatorio->status ?? 'pendente');

                $isEmPatrulha = $status === 'em_patrulha';
                $isPendente   = $status === 'pendente';
                $isAprovado   = $status === 'aprovado';
                $isReprovado  = $status === 'reprovado';

                $isEmAndamento = empty($relatorio->final_patrulhamento);

                $rowCls = $isAprovado ? 'is-ok'
                  : ($isReprovado ? 'is-bad'
                  : ($isEmPatrulha ? 'is-live'
                  : ($isEmAndamento ? 'is-live' : 'is-pending')));

                $obsDecisao = $relatorio->decisao_obs ?? $relatorio->observacao ?? null;

                $aprovadoPor = $relatorio->aprovado_por ?? null;
                $reprovadoPor = $relatorio->reprovado_por ?? null;

                $apU = ($aprovadoPor && isset($usersById)) ? ($usersById[(int)$aprovadoPor] ?? null) : null;
                $rpU = ($reprovadoPor && isset($usersById)) ? ($usersById[(int)$reprovadoPor] ?? null) : null;

                $dataFmt = $relatorio->data_patrulhamento
                  ? \Carbon\Carbon::parse($relatorio->data_patrulhamento)->format('d/m/Y')
                  : '—';

                $inicioFmt = $relatorio->inicio_patrulhamento
                  ? \Carbon\Carbon::parse($relatorio->inicio_patrulhamento)->format('H:i')
                  : '—';

                $finalFmt = $relatorio->final_patrulhamento
                  ? \Carbon\Carbon::parse($relatorio->final_patrulhamento)->format('H:i')
                  : null;

                $authRow = auth()->check();
                $nivel = $authRow ? (int)(auth()->user()->nivel ?? 0) : 0;

                $canEncerrarProprio = $isEmPatrulha
                  && empty($relatorio->final_patrulhamento)
                  && $authRow
                  && (int)$relatorio->user_id === (int)auth()->id();

                $canEncerrarAdmin = $isEmPatrulha
                  && empty($relatorio->final_patrulhamento)
                  && $authRow
                  && $nivel >= 7
                  && !$canEncerrarProprio;

                $canEncerrar = $canEncerrarProprio || $canEncerrarAdmin;

                $authRow = auth()->check();
                $canDecide = $authRow && auth()->user()->can('decide', $relatorio);

                $cRG = preg_replace('/\D+/', '', (string)($relatorio->qra_chefe ?? ''));
                $cU  = ($cRG !== '' && isset($usersByRg)) ? ($usersByRg[$cRG] ?? null) : null;
              @endphp

              <tr class="{{ $rowCls }}">
                <td>
                  <span class="rep-id__pill">#{{ $relatorio->id }}</span>
                </td>

                <td>{{ $dataFmt }}</td>

                <td class="rep-mono">{{ $inicioFmt }}</td>

                <td>
                  @if($finalFmt)
                    <span class="rep-mono">{{ $finalFmt }}</span>
                  @else
                    @if($isEmPatrulha)
                      <span class="rep-live"><span class="rep-live__dot"></span> Em patrulha</span>
                    @else
                      <span class="rep-live"><span class="rep-live__dot"></span> Em andamento</span>
                    @endif
                  @endif
                </td>

                <td>
                  <div class="rep-unit__top" title="{{ $relatorio->unidade }}">{{ $relatorio->unidade }}</div>
                </td>

                <td>
                  @if($cU)
                    <div class="fw-semibold rep-ellipsis" title="{{ $cU->name }}">{{ $cU->name }}</div>
                    <div class="text-muted small rep-ellipsis" title="{{ ($cU->cargo ?? '—') . ' • ' . $cRG }}">
                      {{ $cU->cargo ?? '—' }} • <span class="rep-mono">{{ $cRG }}</span>
                    </div>
                  @else
                    <span class="rep-mono">{{ $cRG ?: '—' }}</span>
                  @endif
                </td>

                <td class="rep-status-cell">
                  <div class="rep-status">
                    @if($isEmPatrulha)
                      <span class="rep-badge-status rep-badge--info">
                        <span class="rep-dot"></span>
                        Unidade em patrulha
                      </span>
                      <div class="rep-aux text-muted rep-aux--tight rep-ellipsis" title="Auto-save ativo na tela de visualização.">
                        Auto-save ativo na tela de visualização.
                      </div>

                    @elseif($isPendente)
                      <span class="rep-badge-status rep-badge--warn">Pendente</span>

                    @elseif($isAprovado)
                      <span class="rep-badge-status rep-badge--ok">Aprovado</span>

                    @else
                      <span class="rep-badge-status rep-badge--bad">Reprovado</span>
                    @endif

                    @if(!empty($obsDecisao) && !$isEmPatrulha)
                      <div class="rep-aux text-muted rep-ellipsis" title="{{ $obsDecisao }}">
                        <b>Obs:</b> {{ \Illuminate\Support\Str::limit($obsDecisao, 120) }}
                      </div>
                    @endif

                    @if($isAprovado && $aprovadoPor)
                      <div
                        class="rep-aux text-muted rep-ellipsis"
                        title="@if($apU) {{ $apU->name }} ({{ $apU->cargo ?? '—' }}) • {{ $apU->rg ?? '—' }} @else ID {{ $aprovadoPor }} @endif"
                      >
                        <b>Aprovado por:</b>
                        @if($apU)
                          {{ $apU->name }}
                          <span class="text-muted">({{ $apU->cargo ?? '—' }})</span>
                          • <span class="rep-mono">{{ $apU->rg ?? '—' }}</span>
                        @else
                          <span class="rep-mono">ID {{ $aprovadoPor }}</span>
                        @endif
                      </div>
                    @endif

                    @if($isReprovado && $reprovadoPor)
                      <div
                        class="rep-aux text-muted rep-ellipsis"
                        title="@if($rpU) {{ $rpU->name }} ({{ $rpU->cargo ?? '—' }}) • {{ $rpU->rg ?? '—' }} @else ID {{ $reprovadoPor }} @endif"
                      >
                        <b>Reprovado por:</b>
                        @if($rpU)
                          {{ $rpU->name }}
                          <span class="text-muted">({{ $rpU->cargo ?? '—' }})</span>
                          • <span class="rep-mono">{{ $rpU->rg ?? '—' }}</span>
                        @else
                          <span class="rep-mono">ID {{ $reprovadoPor }}</span>
                        @endif
                      </div>
                    @endif
                  </div>
                </td>

                <td class="rep-actions-cell">
                  <div class="rep-actions-top">
                    <a href="{{ route('relatorios.show', $relatorio) }}?back={{ $backEncoded }}"
                       class="btn btn-sm btn-outline-primary rep-action-btn">
                      Ver
                    </a>

                    @if($canEncerrar)
                      <form
                        method="POST"
                        action="{{ route('relatorios.encerrar_turno', $relatorio) }}"
                        class="rep-inline js-encerrar-turno-form"
                        data-confirm-message="{{ $canEncerrarAdmin ? 'Encerrar o patrulhamento desta unidade como supervisor/administrador?' : 'Encerrar turno e enviar para validação (status pendente)?' }}"
                      >
                        @csrf
                        <button
                          class="btn btn-sm btn-outline-success rep-action-btn js-encerrar-turno-btn"
                          type="submit"
                          data-loading-title="{{ $canEncerrarAdmin ? 'Fechando patrulha...' : 'Encerrando turno...' }}"
                          data-loading-sub="{{ $canEncerrarAdmin ? 'Aguarde enquanto o sistema finaliza esta patrulha e atualiza o status do relatório.' : 'Aguarde enquanto o sistema encerra o turno e envia o relatório para validação.' }}"
                          data-loading-button="{{ $canEncerrarAdmin ? 'Fechando patrulha...' : 'Encerrando turno...' }}"
                        >
                          {{ $canEncerrarAdmin ? 'Fechar patrulha' : 'Encerrar' }}
                        </button>
                      </form>
                    @endif

                    @if(($isAprovado || $isReprovado) && $canDecide && Route::has('relatorios.edit'))
                      <a href="{{ route('relatorios.edit', $relatorio) }}?back={{ $backEncoded }}"
                         class="btn btn-sm btn-outline-warning rep-action-btn">
                        Editar
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">
                  Nenhum relatório encontrado.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($hasPaginator)
      <div class="rep-pagination">
        <div class="rep-pagination__meta">
          Mostrando <b>{{ $from }}</b> a <b>{{ $to }}</b> de <b>{{ $totalAll }}</b> resultados
        </div>

        <div class="ms-auto">
          {{ $relatorios->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif

    <div class="rep-foot">
      <div>
        <span class="rep-foot__dot"></span>
        Dados internos • Controle de auditoria
      </div>
      <div>GRR • PRF — fivem.bc</div>
    </div>
  </div>

</div>

<div class="modal fade" id="modalIniciarTurno" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Iniciar Turno</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar" id="btnCloseModalTurno"></button>
      </div>

      <form method="POST" action="{{ route('relatorios.iniciar_turno') }}" id="formIniciarTurno">
        @csrf

        <div class="modal-body">
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Unidade *</label>

              <select name="unidade_select" class="form-select" id="unidadeSelect" required>
                <option value="">Selecione</option>
                @foreach($unidades as $u)
                  <option value="{{ $u }}" @selected(!$unidadeIsOther && $oldUnidade === $u)>{{ $u }}</option>
                @endforeach
                <option value="__OUTRA__" @selected($unidadeIsOther)>Outra (digitar)</option>
              </select>

              <input type="hidden" name="unidade" id="unidadeReal" value="{{ $oldUnidade }}">

              <div class="text-muted small mt-1">Escolha a unidade de patrulhamento.</div>

              <div class="mt-2" id="unidadeOutraWrap" style="{{ $unidadeIsOther ? '' : 'display:none;' }}">
                <input id="unidadeOutra" class="form-control" placeholder="Digite a unidade"
                       value="{{ $unidadeIsOther ? $oldUnidade : '' }}">
                <div class="text-muted small mt-1">Use o padrão do sistema. Ex.: GRR-02 CRD.</div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Chefe da barca (RG) *</label>

              <input
                name="qra_chefe"
                class="form-control js-rg-lookup"
                data-role="Chefe"
                data-target="#rgInfoChefe"
                required
                inputmode="numeric"
                placeholder="Somente números"
                value="{{ old('qra_chefe', $rgAuth) }}"
                {{ $rgAuth !== '' ? 'readonly' : '' }}
              >

              <div id="rgInfoChefe" class="small mt-1"></div>

              <div class="text-muted small mt-1">
                @if($rgAuth !== '')
                  Preenchido automaticamente com seu RG.
                @else
                  Informe o RG do chefe da barca.
                @endif
              </div>
            </div>

            <div class="col-md-6" id="wrapMotorista">
              <label class="form-label">
                Motorista (RG) <span class="text-danger" id="motoristaReqStar">*</span>
              </label>

              <input
                name="motorista"
                id="inputMotorista"
                class="form-control js-rg-lookup"
                data-role="Motorista"
                data-target="#rgInfoMotorista"
                required
                inputmode="numeric"
                placeholder="P1 (motorista)"
                value="{{ old('motorista') }}"
              >

              <div id="rgInfoMotorista" class="small mt-1"></div>

              <div class="text-muted small mt-1" id="motoristaHint">
                Necessário para unidades operacionais.
              </div>
            </div>

            <div class="col-md-6" id="wrapP3">
              <label class="form-label">P3 (RG)</label>
              <input
                name="terceiro"
                id="inputP3"
                class="form-control js-rg-lookup"
                data-role="P3"
                data-target="#rgInfoP3"
                inputmode="numeric"
                placeholder="Auxiliar P3 (opcional)"
                value="{{ old('terceiro') }}"
              >
              <div id="rgInfoP3" class="small mt-1"></div>
            </div>

            <div class="col-md-6" id="wrapP4">
              <label class="form-label">P4 (RG)</label>
              <input
                name="quarto"
                id="inputP4"
                class="form-control js-rg-lookup"
                data-role="P4"
                data-target="#rgInfoP4"
                inputmode="numeric"
                placeholder="Segurança P4 (opcional)"
                value="{{ old('quarto') }}"
              >
              <div id="rgInfoP4" class="small mt-1"></div>
            </div>

            <div class="col-md-6" id="wrapP5">
              <label class="form-label">P5 (RG)</label>
              <input
                name="quinto"
                id="inputP5"
                class="form-control js-rg-lookup"
                data-role="P5"
                data-target="#rgInfoP5"
                inputmode="numeric"
                placeholder="Segurança P5 (opcional)"
                value="{{ old('quinto') }}"
              >
              <div id="rgInfoP5" class="small mt-1"></div>
            </div>

            <div class="col-12">
              <div class="alert alert-light border mb-0">
                <b>Importante:</b> o horário de início será gravado automaticamente pelo relógio do site.
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="btnCancelarModalTurno">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-bold" id="btnConfirmarTurno">
            <span class="btn-label">Confirmar e iniciar</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="turnoLoadingOverlay" class="turno-loading-overlay d-none" aria-live="assertive" aria-busy="true">
  <div class="turno-loading-card">
    <div class="turno-loading-spinner"></div>
    <div class="turno-loading-title" id="turnoLoadingTitle">Iniciando turno...</div>
    <div class="turno-loading-sub" id="turnoLoadingSub">
      Aguarde enquanto o sistema cria o relatório e abre a tela de patrulhamento.
    </div>
  </div>
</div>

@if($errors->any())
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      try{
        const el = document.getElementById('modalIniciarTurno');
        if(!el) return;
        const modal = new bootstrap.Modal(el);
        modal.show();
      }catch(e){}
    });
  </script>
@endif

<script>
  function onlyDigits(v){ return (v || '').toString().replace(/\D+/g, ''); }

  const debounceMap = new WeakMap();
  function debounceInput(input, fn, ms=350){
    const old = debounceMap.get(input);
    if(old) clearTimeout(old);
    const t = setTimeout(fn, ms);
    debounceMap.set(input, t);
  }

  (function(){
    const modalEl = document.getElementById('modalIniciarTurno');

    function syncUnidade(){
      const sel   = document.getElementById('unidadeSelect');
      const real  = document.getElementById('unidadeReal');
      const wrap  = document.getElementById('unidadeOutraWrap');
      const other = document.getElementById('unidadeOutra');

      if(!sel || !real) return;

      const v = (sel.value || '').trim();

      if(v === '__OUTRA__'){
        if(wrap) wrap.style.display = '';
        const typed = (other?.value || '').trim();
        real.value = typed;
      } else {
        if(wrap) wrap.style.display = 'none';
        real.value = v;
      }

      applyAdminMode();
      refreshSubmitState();
    }

    document.addEventListener('DOMContentLoaded', function(){
      const sel   = document.getElementById('unidadeSelect');
      const other = document.getElementById('unidadeOutra');

      sel?.addEventListener('change', syncUnidade);
      other?.addEventListener('input', syncUnidade);

      syncUnidade();
    });

    if(modalEl){
      modalEl.addEventListener('shown.bs.modal', function(){
        syncUnidade();
        checkDuplicates();
        refreshSubmitState();
      });
    }
  })();

  function isAdministrativoSelected(){
    const u = (document.getElementById('unidadeReal')?.value || '').trim().toLowerCase();
    return u === 'administrativo';
  }

  function applyAdminMode(){
    const admin = isAdministrativoSelected();

    const wP3 = document.getElementById('wrapP3');
    const wP4 = document.getElementById('wrapP4');
    const wP5 = document.getElementById('wrapP5');

    const inpMotorista = document.getElementById('inputMotorista');
    const inpChefe     = document.querySelector('#modalIniciarTurno input[name="qra_chefe"]');

    const star = document.getElementById('motoristaReqStar');
    const hint = document.getElementById('motoristaHint');

    [wP3,wP4,wP5].forEach(el => {
      if(!el) return;
      el.classList.toggle('adm-hidden', admin);
    });

    if(inpMotorista){
      if(admin){
        inpMotorista.removeAttribute('required');
        if(star) star.style.display = 'none';
        if(hint) hint.textContent = 'Administrativo: não precisa de 2 pessoas.';
        const rgM = onlyDigits(inpMotorista.value);
        const rgC = onlyDigits(inpChefe?.value || '');
        if(rgM && rgC && rgM === rgC){
          inpMotorista.value = '';
          inpMotorista.dispatchEvent(new Event('input', { bubbles:true }));
        }
      } else {
        inpMotorista.setAttribute('required','required');
        if(star) star.style.display = '';
        if(hint) hint.textContent = 'Necessário para unidades operacionais.';
      }
    }

    if(admin){
      ['inputP3','inputP4','inputP5'].forEach(id => {
        const el = document.getElementById(id);
        if(el && el.value){
          el.value = '';
          el.dispatchEvent(new Event('input', { bubbles:true }));
        }
      });
    }

    checkDuplicates();
    refreshSubmitState();
  }

  const RG_LOOKUP_URL = @json(route('usuarios.por_rg'));

  async function lookupRg(rg){
    const url = RG_LOOKUP_URL + '?rg=' + encodeURIComponent(rg);
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
    return await res.json();
  }

  function setInfo(el, html, kind){
    if(!el) return;
    el.innerHTML = html || '';
    el.classList.remove('text-muted','text-success','text-danger');
    if(kind === 'muted') el.classList.add('text-muted');
    if(kind === 'ok')    el.classList.add('text-success');
    if(kind === 'bad')   el.classList.add('text-danger');
  }

  function getAllRgInputs(){
    const all = Array.from(document.querySelectorAll('#modalIniciarTurno .js-rg-lookup'));
    return all.filter(i => {
      const wrap = i.closest('.col-md-6');
      if(!wrap) return true;
      return !wrap.classList.contains('adm-hidden');
    });
  }

  function roleOf(input){
    return input.getAttribute('data-role') || 'RG';
  }

  function infoElOf(input){
    const sel = input.getAttribute('data-target');
    return sel ? document.querySelector(sel) : null;
  }

  function setDupError(input, msg){
    input.dataset.dupError = '1';
    input.classList.add('is-invalid');
    setInfo(infoElOf(input), msg, 'bad');
  }

  function clearDupError(input){
    delete input.dataset.dupError;
  }

  function setLookupError(input, msg){
    input.dataset.lookupOk = '0';
    input.classList.add('is-invalid');
    setInfo(infoElOf(input), msg, 'bad');
  }

  function setLookupOk(input, name, cargo){
    input.dataset.lookupOk = '1';
    input.classList.remove('is-invalid');
    setInfo(infoElOf(input), `<b>${name}</b> <span class="text-muted">(${cargo || '—'})</span>`, 'ok');
  }

  function setLookupNeutral(input){
    delete input.dataset.lookupOk;
    if(input.dataset.dupError !== '1'){
      input.classList.remove('is-invalid');
    }
    setInfo(infoElOf(input), '', 'muted');
  }

  function refreshSubmitState(){
    const btn = document.getElementById('btnConfirmarTurno');
    if(!btn) return;

    if(btn.dataset.submitting === '1') {
      btn.disabled = true;
      return;
    }

    const inputs = getAllRgInputs();
    let invalid = false;

    const unidadeReal = (document.getElementById('unidadeReal')?.value || '').trim();
    if(!unidadeReal) invalid = true;

    for(const i of inputs){
      const rg = onlyDigits(i.value);
      const required = i.hasAttribute('required');

      if(required && rg === ''){
        invalid = true; break;
      }

      if(i.dataset.dupError === '1'){
        invalid = true; break;
      }

      if(rg !== '' && i.dataset.lookupOk === '0'){
        invalid = true; break;
      }
    }

    btn.disabled = invalid;
  }

  function checkDuplicates(){
    const inputs = getAllRgInputs();

    for(const inp of inputs){
      if(inp.dataset.dupError === '1'){
        clearDupError(inp);
        if(inp.dataset.lookupOk === '0'){
          inp.classList.add('is-invalid');
        } else {
          inp.classList.remove('is-invalid');
        }
      }
    }

    const map = new Map();

    for(const inp of inputs){
      const rg = onlyDigits(inp.value);
      if(!rg) continue;

      if(!map.has(rg)){
        map.set(rg, inp);
      } else {
        const first = map.get(rg);
        setDupError(inp,   `RG duplicado: já foi usado em <b>${roleOf(first)}</b>.`);
        setDupError(first, `RG duplicado: já foi usado em <b>${roleOf(inp)}</b>.`);
      }
    }

    refreshSubmitState();
  }

  function bindRgLookup(input){
    const infoEl = infoElOf(input);

    function run(){
      const rg = onlyDigits(input.value);
      input.value = rg;

      if(!rg || rg.length < 3){
        setLookupNeutral(input);
        checkDuplicates();
        refreshSubmitState();
        return;
      }

      if(infoEl) setInfo(infoEl, 'Consultando...', 'muted');

      lookupRg(rg)
        .then(data => {
        if(data && data.found){

          if(data.blocked){
            setLookupError(input, data.reason || 'Este policial não pode patrulhar.');
          } else {
            setLookupOk(input, data.name, data.cargo);
          }

        } else {
          setLookupError(input, 'RG não encontrado no efetivo.');
        }

        checkDuplicates();
        refreshSubmitState();
      })
        .catch(() => {
          setLookupError(input, 'Erro ao consultar RG. Tente novamente.');
          checkDuplicates();
          refreshSubmitState();
        });
    }

    input.addEventListener('input', () => {
      debounceInput(input, run, 350);
      debounceInput(input, () => { checkDuplicates(); refreshSubmitState(); }, 120);
    });

    input.addEventListener('blur', () => {
      run();
      checkDuplicates();
      refreshSubmitState();
    });

    if((input.value || '').trim() !== ''){
      debounceInput(input, run, 50);
    }
  }

  document.querySelectorAll('#modalIniciarTurno .js-rg-lookup').forEach(bindRgLookup);

  function setTurnoLoadingText(title, sub){
    const titleEl = document.getElementById('turnoLoadingTitle');
    const subEl   = document.getElementById('turnoLoadingSub');

    if(titleEl) titleEl.textContent = title || 'Processando...';
    if(subEl)   subEl.textContent   = sub || 'Aguarde enquanto o sistema conclui a operação.';
  }

  function activateTurnoLoading(options = {}){
    const overlay = document.getElementById('turnoLoadingOverlay');
    const btn = options.button || document.getElementById('btnConfirmarTurno');
    const btnClose = document.getElementById('btnCloseModalTurno');
    const btnCancel = document.getElementById('btnCancelarModalTurno');

    setTurnoLoadingText(options.title, options.sub);

    if(btn){
      btn.dataset.submitting = '1';
      btn.disabled = true;
      btn.innerHTML = `<span class="turno-btn-spinner"></span><span>${options.buttonText || 'Processando...'}</span>`;
    }

    if(btnClose) btnClose.disabled = true;
    if(btnCancel) btnCancel.disabled = true;

    document.body.classList.add('turno-is-submitting');

    if(overlay){
      overlay.classList.remove('d-none');
    }
  }

  function bindEncerrarTurnoForms(){
    document.querySelectorAll('.js-encerrar-turno-form').forEach(form => {
      if(form.dataset.boundSubmit === '1') return;
      form.dataset.boundSubmit = '1';

      form.addEventListener('submit', function(e){
        const btn = form.querySelector('.js-encerrar-turno-btn');
        const confirmMessage = form.dataset.confirmMessage || 'Deseja continuar?';

        if(btn && btn.dataset.submitting === '1'){
          e.preventDefault();
          return;
        }

        if(!window.confirm(confirmMessage)){
          e.preventDefault();
          return;
        }

        activateTurnoLoading({
          button: btn,
          buttonText: btn?.dataset.loadingButton || btn?.textContent?.trim() || 'Processando...',
          title: btn?.dataset.loadingTitle || 'Encerrando turno...',
          sub: btn?.dataset.loadingSub || 'Aguarde enquanto o sistema encerra o turno e atualiza o relatório.'
        });
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function(){
    applyAdminMode();
    checkDuplicates();
    refreshSubmitState();
    bindEncerrarTurnoForms();
  });

  document.getElementById('formIniciarTurno')?.addEventListener('submit', function(e){
    const btn = document.getElementById('btnConfirmarTurno');

    if(btn && btn.dataset.submitting === '1'){
      e.preventDefault();
      return;
    }

    applyAdminMode();
    checkDuplicates();
    refreshSubmitState();

    if(btn && btn.disabled){
      e.preventDefault();
      alert('Não é possível iniciar: existe RG inválido, RG duplicado ou unidade não selecionada.');
      return;
    }

    activateTurnoLoading({
      button: btn,
      buttonText: 'Iniciando turno...',
      title: 'Iniciando turno...',
      sub: 'Aguarde enquanto o sistema cria o relatório e abre a tela de patrulhamento.'
    });
  });
</script>
@endsection