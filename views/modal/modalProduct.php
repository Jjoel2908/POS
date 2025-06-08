<!-- # [ H E A D E R ] # -->
<?php
$formId = "formProduct";
$module = "Producto";
$modalClass = "view-modal";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="col-md-12 view-form">
    <label class="mb-1" for="nombre">Nombre</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="hidden" name="id" id="id">
        <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Ingrese el nombre del producto" oninput="validateAndConvertToUppercase(event)" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-chevron-right"></i></span>
    </div>
</div>

<div class="col-md-12 col-lg-6 view-form">
    <label class="mb-1" for="id_marca">Marca</label>
    <select name="id_marca" id="id_marca" class="form-control select" required>
    </select>
</div>

<div id="container-code" class="col-md-12 col-lg-6 view-form">
    <label class="mb-1" for="codigo">Código</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="text" name="codigo" id="codigo" placeholder="Código del producto" oninput="validateString(event)" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="bx bx-barcode"></i></span>
    </div>
</div>

<div class="col-md-12 col-lg-6 view-form">
    <label class="mb-1" for="modelo">Modelo</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="text" name="modelo" id="modelo" placeholder="Modelo del producto" oninput="validateString(event)">
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-tag"></i></span>
    </div>
</div>

<div class="col-md-12 col-lg-6 view-form">
  <label class="mb-1" for="id_talla">Talla / Presentación</label>
  <select name="id_talla" id="id_talla" class="form-control select">
    <!-- Tallas de ropa (letras) -->
    <option value="1">XXXS</option>
    <option value="2">XXS</option>
    <option value="3">XS</option>
    <option value="4">S</option>
    <option value="5">M</option>
    <option value="6">L</option>
    <option value="7">XL</option>
    <option value="8">XXL</option>
    <option value="9">XXXL</option>
    <option value="10">4XL</option>
    <option value="11">5XL</option>

    <!-- Tallas numéricas comunes (ropa y calzado) -->
    <option value="12">22</option>
    <option value="13">22.5</option>
    <option value="14">23</option>
    <option value="15">23.5</option>
    <option value="16">24</option>
    <option value="17">24.5</option>
    <option value="18">25</option>
    <option value="19">25.5</option>
    <option value="20">26</option>
    <option value="21">26.5</option>
    <option value="22">27</option>
    <option value="23">27.5</option>
    <option value="24">28</option>
    <option value="25">28.5</option>
    <option value="26">29</option>
    <option value="27">29.5</option>
    <option value="28">30</option>
    <option value="29">30.5</option>
    <option value="30">31</option>
    <option value="31">32</option>
    <option value="32">33</option>
    <option value="33">34</option>
    <option value="34">35</option>
    <option value="35">36</option>
    <option value="36">37</option>
    <option value="37">38</option>
    <option value="38">39</option>
    <option value="39">40</option>

    <!-- Presentaciones de productos líquidos o cosméticos -->
    <option value="40">15 ML</option>
    <option value="41">30 ML</option>
    <option value="42">50 ML</option>
    <option value="43">75 ML</option>
    <option value="44">100 ML</option>
    <option value="45">125 ML</option>
    <option value="46">150 ML</option>
    <option value="47">200 ML</option>

    <!-- Genéricas -->
    <option value="48">CHICA</option>
    <option value="49">MEDIANA</option>
    <option value="50">GRANDE</option>
    <option value="51">UNITALLA</option>
    <option value="52">SIN TALLA</option>
  </select>
</div>

<div class="col-md-12 col-lg-6 view-form">
  <label class="mb-1" for="id_color">Color</label>
  <select name="id_color" id="id_color" class="form-control select">
    <option value="1">NEGRO</option>
    <option value="2">BLANCO</option>
    <option value="3">ROJO</option>
    <option value="4">AZUL</option>
    <option value="5">VERDE</option>
    <option value="6">AMARILLO</option>
    <option value="7">NARANJA</option>
    <option value="8">ROSA</option>
    <option value="9">MORADO</option>
    <option value="10">GRIS</option>
    <option value="11">CAFÉ</option>
    <option value="12">BEIGE</option>
    <option value="13">TURQUESA</option>
    <option value="14">VINO</option>
    <option value="15">DORADO</option>
    <option value="16">PLATEADO</option>
    <option value="17">FUCSIA</option>
    <option value="18">AQUA</option>
    <option value="19">CORAL</option>
    <option value="20">LILA</option>
    <option value="21">MARFIL</option>
    <option value="22">OLIVA</option>
    <option value="23">MOSTAZA</option>
    <option value="24">CELESTE</option>
    <option value="25">LAVANDA</option>
    <option value="26">GRANATE</option>
    <option value="27">PÚRPURA</option>
    <option value="28">TERRACOTA</option>
    <option value="29">CIAN</option>
    <option value="30">OTRO</option>
  </select>
</div>

<div class="col-md-12 col-lg-6 view-form">
    <label class="mb-1" for="imagen">Imagen</label>
    <input class="form-control" type="hidden" name="current_image" id="current_image">
    <input class="form-control" type="file" name="imagen" id="imagen" accept="image/png, image/jpeg, image/jpg, image/webp">
</div>

<div class="col-md-6 view-form">
    <label class="mb-1" for="precio_compra">Precio Compra</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="number" step="0.01" min="0" name="precio_compra" id="precio_compra" placeholder="Precio Compra" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-sack-dollar"></i></span>
    </div>
</div>

<div class="col-md-6 view-form">
    <label class="mb-1" for="precio_venta">Precio Venta</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="number" step="0.01" min="0" name="precio_venta" id="precio_venta" placeholder="Precio Venta" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-sack-dollar"></i></span>
    </div>
</div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>