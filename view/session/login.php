<div container='center'>
    <div login="content" position="relative">
        <div login="title" text-align="center">Inicio de Sesion</div>
        <div login="description">
            <div class="form-group">
                <label class="form-label"><i class="fas fa-user"></i> Usuario</label>
                <input type="text" id="username" class="form-control" />
            </div>
            <br />
            <div class="form-group">
                <label class="form-label"><i class="fas fa-lock-open"></i> Contrase&ntilde;a</label>
                <input type="password" id="password" class="form-control" />
            </div>
            {!__token!}
            <br/>
            <div text-align="center">
                <button id="login" class="btn btn-outline-primary">Acceder</button>
            </div>
        </div>
        <div login="footer" id="footer"></div>
    </div>
</div>