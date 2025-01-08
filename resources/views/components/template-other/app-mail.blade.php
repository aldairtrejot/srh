<div id="_modalChangeMail" class="modal-backdrop" style="display: none;">
    <div class="modal-content" style="width: 430px; height: 380px;">
        <h3
            style="font-weight: bold; color: #10312b; text-align: left; display: flex; align-items: center; margin-bottom: 20px;">
            <i class="fa fa-mail-forward" style="font-size: 20px; color: #10312b; margin-right: 10px;"></i>
            Enviar email
        </h3>

        <p>Ingresa el nombre y correo del remitente para enviar la información del No. de turno: <label
                id="noTurnoSistemaEmail"></label>, para su seguimiento.</p>

        <div class="custom-input-container">
            <label class="custom-input-label" for="customTextInput">Nombre</label>
            <input type="password" id="emailName" class="custom-input-field" autocomplete="current-password">
        </div>

        <div class="custom-input-container">
            <label class="custom-input-label" for="customTextInput">Email</label>
            <input type="password" id="emailMail" name="newPassword" class="custom-input-field"
                autocomplete="current-password">
        </div>

        <div class="modal-buttons">
            <button type="button" id="_cancelEmail">Cancelar</button>
            <button onclick="validateEmail()" style="font-weight: bold; color: #10312b;"
                id="_confirmAction">Confirmar</button>
        </div>
    </div>
</div>