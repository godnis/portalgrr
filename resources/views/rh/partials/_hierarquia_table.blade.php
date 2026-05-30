@php
  $list = collect($list ?? []);
  $tableId = $tableId ?? 'rhTable';
@endphp

{{-- Scroll horizontal no TOPO (sincronizado) --}}
<div class="rhH-scrollTop" id="rhHScrollTop-{{ $tableId }}">
  <div class="rhH-scrollTop__inner" id="rhHScrollTopInner-{{ $tableId }}"></div>
</div>

<div class="rhH-tableWrap" id="rhHTableWrap-{{ $tableId }}">
  <table class="table rhH-table align-middle mb-0" id="rhHTable-{{ $tableId }}">
    <thead>
      <tr>
        <th class="sticky-col sticky-col--1" style="min-width:320px;">Militar</th>
        <th class="sticky-col sticky-col--2" style="min-width:110px;">Equipe</th>
        <th style="min-width:190px;">Cargo</th>
        <th style="min-width:140px;">Status</th>
        <th style="min-width:120px;">Admissão</th>
        <th style="min-width:130px;">Últ. Promoção</th>
        <th style="min-width:110px;">Serial</th>

        <th style="min-width:180px;">Discord ID</th>
        <th style="min-width:320px;">Função / Observação</th>
        <th style="min-width:180px;">Medalhas</th>
        <th style="min-width:180px;">Alinhamento</th>

        <th class="text-center" style="min-width:70px;">Instr.</th>

        <th class="text-center" title="Procedimentos Operacionais Padrão">POP</th>
        <th class="text-center" title="CLT">CLT</th>
        <th class="text-center" title="CAP">CAP</th>
        <th class="text-center" title="CTB">CTB</th>
        <th class="text-center" title="CTA">CTA</th>
        <th class="text-center" title="SAT-B">SAT-B</th>
        <th class="text-center" title="BOPM">BOPM</th>
        <th class="text-center" title="GMP">GMP</th>
        <th class="text-center" title="DOA">DOA</th>

        <th class="text-end" style="min-width:190px;">Ações</th>
      </tr>
    </thead>

    <tbody>
      @forelse($list as $r)
        @php
          $statusLabel = match($r->status){
            'em_exercicio' => 'Em Exercício',
            'em_licenca'   => 'Em Licença',
            'desligado'    => 'Desligado',
            'estagio'      => 'Estágio',
            default        => $r->status ?: '—'
          };

          $statusClass = match($r->status){
            'em_exercicio' => 'st st--ok',
            'em_licenca'   => 'st st--warn',
            'desligado'    => 'st st--bad',
            'estagio'      => 'st st--soft',
            default        => 'st st--soft'
          };

          $yes = fn($b) => $b
            ? '<span class="pill pill--yes">✓</span>'
            : '<span class="pill pill--no">—</span>';

          // avatar (iniciais)
          $nome = (string) ($r->nome ?? '');
          $parts = preg_split('/\s+/', trim($nome));
          $ini = '';
          if (!empty($parts[0])) $ini .= mb_strtoupper(mb_substr($parts[0], 0, 1));
          if (!empty($parts[1])) $ini .= mb_strtoupper(mb_substr($parts[1], 0, 1));
          if ($ini === '') $ini = 'PRF';

          $discord = $r->discord_id ?? null;
          $funcObs = $r->funcao_obs ?? null;
          $medalhas = $r->medalhas ?? null;
          $alinhamento = $r->alinhamento ?? null;

          // ✅ FOTO (User->avatar_url)
          $avatarUrl = $r->user?->avatar_url ?? null;
        @endphp

        <tr>
          <td class="sticky-col sticky-col--1">
            <div class="rhH-person">
              <div class="rhH-avatar">
                @if($avatarUrl)
                  <img src="{{ $avatarUrl }}" class="rhH-photo" alt="Foto">
                @else
                  {{ $ini }}
                @endif
              </div>

              <div class="rhH-person__meta">
                <div class="rhH-person__name">{{ $r->nome }}</div>
                <div class="rhH-person__sub">
                  @if($r->cpf) <span><b>CPF:</b> {{ $r->cpf }}</span> @endif
                  @if($r->user?->rg)
                    <span class="sep">•</span>
                    <span><b>RG:</b> {{ $r->user->rg }}</span>
                  @endif
                </div>
              </div>
            </div>
          </td>

          <td class="sticky-col sticky-col--2">
            <span class="badge text-bg-light border">{{ $r->equipe ?? '-' }}</span>
          </td>

          <td>
            <div class="fw-semibold">{{ $r->cargo ?? '-' }}</div>
            <div class="small text-muted">{{ $r->efetivacao ?? '' }}</div>
          </td>

          <td>
            <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
          </td>

          <td class="small text-muted">{{ $r->admissao?->format('d/m/Y') ?? '-' }}</td>
          <td class="small text-muted">{{ $r->ultima_promocao?->format('d/m/Y') ?? '-' }}</td>
          <td class="small text-muted">{{ $r->serial ?? '-' }}</td>

          <td class="small">
            @if($discord)
              <span class="copy" data-copy="{{ $discord }}">{{ $discord }}</span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          <td style="white-space: normal; min-width:320px;">
            @if($funcObs)
              <div class="rhH-person__obs">{{ $funcObs }}</div>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          <td>
            @if($medalhas)
              <span class="tag">🏅 {{ $medalhas }}</span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          <td>
            @if($alinhamento)
              <span class="tag">🎯 {{ $alinhamento }}</span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          <td class="text-center">
            {!! $r->instrutor ? '<span class="pill pill--yes">✓</span>' : '<span class="pill pill--no">—</span>' !!}
          </td>

          <td class="text-center">{!! $yes($r->pop) !!}</td>
          <td class="text-center">{!! $yes($r->clt) !!}</td>
          <td class="text-center">{!! $yes($r->cap) !!}</td>
          <td class="text-center">{!! $yes($r->ctb) !!}</td>
          <td class="text-center">{!! $yes($r->cta) !!}</td>
          <td class="text-center">{!! $yes($r->satb) !!}</td>
          <td class="text-center">{!! $yes($r->bopm) !!}</td>
          <td class="text-center">{!! $yes($r->gmp) !!}</td>
          <td class="text-center">{!! $yes($r->doa) !!}</td>

          <td class="text-end">
            @if($canEdit)
              <div class="rhH-actions">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('rh.hierarquia.edit', $r) }}">Editar</a>

                <form class="d-inline" method="POST" action="{{ route('rh.hierarquia.destroy', $r) }}"
                      onsubmit="return confirm('Remover este registro?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Remover</button>
                </form>
              </div>
            @else
              <span class="text-muted small">somente leitura</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="22" class="text-center text-muted py-5">Nenhum registro encontrado.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="rhH-footerHint">
  Dica: use a barra horizontal no topo para navegar para a direita sem precisar descer.
</div>

<style>
  .rhH-footerHint{
    padding: 12px 16px 16px;
    border-top: 1px solid rgba(2,6,23,.06);
    color: rgba(2,6,23,.55);
    font-size: 12px;
  }

  /* Scroll horizontal no TOPO */
  .rhH-scrollTop{
    overflow-x: auto;
    overflow-y: hidden;
    height: 16px;
    margin-top: 12px;
    border-top: 1px solid rgba(2,6,23,.06);
    border-bottom: 1px solid rgba(2,6,23,.06);
    background: #fff;
  }
  .rhH-scrollTop__inner{ height: 1px; }

  /* Tabela */
  .rhH-tableWrap{ overflow-x: auto; overflow-y: visible; }
  .rhH-table thead th{
    position: sticky; top: 0;
    background: #f8fafc;
    z-index: 3;
    font-size: 11px;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: rgba(2,6,23,.60);
    border-bottom: 1px solid rgba(2,6,23,.08);
    padding: 12px 12px;
    white-space: nowrap;
  }
  .rhH-table tbody td{
    padding: 12px;
    border-top: 1px solid rgba(2,6,23,.06);
    white-space: nowrap;
    background: #fff;
  }

  /* Sticky colunas */
  .sticky-col{ position: sticky; z-index: 2; background:#fff; }
  .sticky-col--1{ left:0; z-index:4; }
  .sticky-col--2{ left:320px; z-index:4; }

  .rhH-person{ display:flex; align-items:flex-start; gap:12px; min-width:320px; }
  .rhH-avatar{
    width:40px; height:40px;
    border-radius:14px;
    display:flex; align-items:center; justify-content:center;
    font-weight:900;
    color:#0b1220;
    background:
      radial-gradient(22px 22px at 30% 25%, rgba(59,130,246,.25), transparent 60%),
      radial-gradient(22px 22px at 70% 75%, rgba(16,185,129,.18), transparent 60%),
      #eef2ff;
    border:1px solid rgba(2,6,23,.10);
    overflow:hidden;
  }
  .rhH-photo{ width:40px; height:40px; object-fit:cover; border-radius:14px; display:block; }

  .rhH-person__name{ font-weight:900; color:#0b1220; line-height:1.1; }
  .rhH-person__sub{ font-size:12px; color:rgba(2,6,23,.60); margin-top:2px; }
  .sep{ margin:0 6px; opacity:.7; }
  .rhH-person__obs{ font-size:12px; color:rgba(2,6,23,.65); margin-top:4px; max-width:520px; white-space:normal; }

  .tag{
    font-size:12px; padding:6px 10px;
    border-radius:999px;
    border:1px solid rgba(2,6,23,.10);
    background:rgba(248,250,252,.95);
    color:rgba(2,6,23,.75);
    display:inline-flex; gap:6px; align-items:center;
  }

  .st{
    display:inline-flex; align-items:center; justify-content:center;
    padding:6px 10px; border-radius:999px;
    font-weight:800; font-size:12px;
    border:1px solid rgba(2,6,23,.10);
    background:#f8fafc; color:rgba(2,6,23,.78);
  }
  .st--ok{ background: rgba(16,185,129,.10); border-color: rgba(16,185,129,.22); color: #0f766e; }
  .st--warn{ background: rgba(245,158,11,.12); border-color: rgba(245,158,11,.25); color: #92400e; }
  .st--bad{ background: rgba(239,68,68,.10); border-color: rgba(239,68,68,.22); color: #991b1b; }
  .st--soft{ background: rgba(148,163,184,.14); border-color: rgba(148,163,184,.28); color: rgba(2,6,23,.72); }

  .pill{
    display:inline-flex; align-items:center; justify-content:center;
    min-width:26px; padding:4px 8px;
    border-radius:10px; font-weight:900; font-size:12px;
    border:1px solid rgba(2,6,23,.10);
    background:#fff; color: rgba(2,6,23,.65);
  }
  .pill--yes{ background: rgba(16,185,129,.12); border-color: rgba(16,185,129,.22); color:#0f766e; }
  .pill--no{ background: rgba(148,163,184,.12); border-color: rgba(148,163,184,.26); color: rgba(2,6,23,.55); }

  .rhH-actions{ display:flex; justify-content:flex-end; gap:8px; }

  .copy{
    cursor:pointer; font-weight:700;
    padding:6px 10px; border-radius:10px;
    border:1px solid rgba(2,6,23,.10);
    background:rgba(248,250,252,.95);
    display:inline-block;
  }
  .copy:hover{ background:#eef2ff; border-color: rgba(59,130,246,.25); }
</style>

<script>
  (function(){
    const wrap = document.getElementById('rhHTableWrap-{{ $tableId }}');
    const table = document.getElementById('rhHTable-{{ $tableId }}');
    const top = document.getElementById('rhHScrollTop-{{ $tableId }}');
    const topInner = document.getElementById('rhHScrollTopInner-{{ $tableId }}');

    if (wrap && table && top && topInner) {
      const syncWidth = () => {
        topInner.style.width = table.scrollWidth + 'px';
      };
      syncWidth();
      window.addEventListener('resize', syncWidth);

      let lock = false;
      top.addEventListener('scroll', () => {
        if (lock) return;
        lock = true;
        wrap.scrollLeft = top.scrollLeft;
        lock = false;
      });

      wrap.addEventListener('scroll', () => {
        if (lock) return;
        lock = true;
        top.scrollLeft = wrap.scrollLeft;
        lock = false;
      });
    }
  })();
</script>
