<footer class="bg-blue py-5">
    <div class="container">
      <div class="row">
        <div class="col-12 col-sm-6 col-lg-4">
          <img src="img/logo.svg" height="45" class="mb-3">
          <p class="white fw-600">Visitanos</p>
          <p class="white"><a class="white" href="https://maps.app.goo.gl/h5R4vcZV46uvUC5k9" target="_blank">Bartolomé
              Mitre 1862, B1604AKZ Florida Oeste, Provincia de Buenos Aires</a></p>
        </div>
        <div class="col-9 col-sm-6 col-lg-3">
          <p class="fw-600 white">Contacto</p>
          <p class="white">4761-5845 / 4730-4554 <br>
            11 6135-8093 (solo WhatsApp)<br>
            <a class="white" href="mailto:info@monoplast.com.ar">info@monoplast.com.ar</a>
          </p>

          <p class="white">Lun a Vie 8 a 13 / 14 a 17 <br>
            Sáb 9:00 a 12:00</p>
        </div>
        <div class="col text-lg-end text-start">
          <div class="instagram ms-lg-auto"><a href="https://www.instagram.com/monoplastsanitarios/?hl=es" target="_blank"><img src="img/instagram.svg" height="20"></a></div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Modal Presupuesto -->
  <div class="modal fade" id="budgetModal" tabindex="-1" aria-labelledby="budgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header">
          <h5 class="modal-title blue" id="budgetModalLabel">Presupuesto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <form id="budgetForm">
            <div class="mb-3">
              <label for="budget_nombre" class="form-label blue font14 fw-600">Nombre y Apellido</label>
              <input type="text" class="form-control rounded-3 border-blue" id="budget_nombre" name="nombre" required>
            </div>
            <div class="mb-3">
              <label for="budget_email" class="form-label blue font14 fw-600">Email</label>
              <input type="email" class="form-control rounded-3 border-blue" id="budget_email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="budget_telefono" class="form-label blue font14 fw-600">Teléfono</label>
              <input type="tel" class="form-control rounded-3 border-blue" id="budget_telefono" name="telefono" required>
            </div>
            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary rounded-5 px-5 py-2">Enviar</button>
            </div>
          </form>
          <div id="budgetResponse" class="mt-3 text-center blue font14 fw-600" style="display:none;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;">
    <!-- Toasts dynamically generated here -->
  </div>

  <script src="js/cart.js"></script>