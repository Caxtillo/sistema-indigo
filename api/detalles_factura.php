<!-- detalles_factura.php -->
<td colspan="7" class="detalle-row">
    <div class="form-row">
        <div class="form-group col-md-1">
            <label for="servicio">Categoría:</label>
            <select class="tipo form-control" name="tipo[]" required>
                <option value="" disabled selected>Selecciona categoría del item</option>
                <option value="servicio" selected>Servicio</option>
                <option value="descuento">Descuento</option>
                <option value="propina">Propina</option>
            </select>
        </div>
        <div class="form-group col-md-1">
            <label for="forma_pago">Pago:</label>
            <select class="forma_pago form-control" name="forma_pago[]" required>
                <option value="" disabled selected>Selecciona una forma de pago</option>
                <option value="efectivo">Efectivo</option>
                <option value="transferencia" selected>Debito/Pago móvil</option>
                <option value="divisa">Divisa</option>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="descripcion">Descripción:</label>
            <input id="descripcion" name="descripcion[]" class="form-control" placeholder="Descripción del servicio/artículo" value="Corte">
        </div>
        <div class="form-group col-md-2">
            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad[]" class="cantidad form-control" placeholder="Cantidad" value=1 required>
        </div>
        <div class="form-group col-md-2">
            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio[]" class="precio form-control" step="0.01" placeholder="Precio del servicio/artículo" required>
        </div>

        <div class="form-group subtotal col-md-1"> <!-- Agrega la clase .subtotal al contenedor del subtotal -->
            <label for="Subtotal">Subtotal:</label>
            <span class="subtotal-amount form-control">0.00</span>
        </div>
                <div class="form-group col-md-1">
            <label for="cantidad">Eliminar item:</label>
            <button type="button" class="eliminar-fila btn btn-danger btn-eliminar-fila">X</button>
        </div>
    </div>
</td>
<script>
// Simula hacer clic en el botón "Agregar Servicio/Artículo" al cargar la página
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('agregar-fila').click();
});
</script>