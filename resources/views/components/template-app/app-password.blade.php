<div id="_modalChangePassword" class="modal-backdrop" style="display: none;">
    <div class="modal-content" style="width: 430px; height: 380px;">
        <h3
            style="font-weight: bold; color: #10312b; text-align: left; display: flex; align-items: center; margin-bottom: 20px;">
            <i class="fas fa-lock" style="font-size: 20px; color: #10312b; margin-right: 10px;"></i>
            Modificar contraseña
        </h3>

        <div class="custom-input-container">
            <label class="custom-input-label" for="customTextInput">Contraseña anterior</label>
            <input type="password" id="oldPassword" class="custom-input-field" autocomplete="current-password">
        </div>

        <div class="custom-input-container">
            <label class="custom-input-label" for="customTextInput">Nueva contraseña</label>
            <input type="password" id="newPassword" name="newPassword" class="custom-input-field"
                autocomplete="current-password">
        </div>

        <div class="custom-input-container">
            <label class="custom-input-label" for="customTextInput">Confirmar contraseña</label>
            <input type="password" id="confirmPassword" class="custom-input-field" autocomplete="current-password">
        </div>

        <div class="modal-buttons">
            <button type="button" id="_cancelAction">Cancelar</button>
            <button onclick="validatePassword()" style="font-weight: bold; color: #10312b;"
                id="_confirmAction">Confirmar</button>
        </div>
    </div>
</div>