<div class="topbar d-flex justify-content-between align-items-center">
    <div class="fw-semibold text-uppercase">
        Polícia Rodoviária Federal
    </div>

    <div class="small">
        Usuário Logado •
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit"
                class="btn btn-link p-0 m-0 align-baseline text-decoration-none">
                Sair
            </button>
        </form>
    </div>
</div>
