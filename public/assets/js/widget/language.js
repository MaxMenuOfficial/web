// assets/widget/language.js

(function waitForMaxMenuConfigAndDOM() {
    // Esperamos a que exista la configuración y los elementos del modal
    if (
      typeof window.MaxMenuConfig === 'undefined' ||
      !document.getElementById('BtnTranslateMenu') ||
      !document.getElementById('translateItemModalMenu')
    ) {
      return requestAnimationFrame(waitForMaxMenuConfigAndDOM);
    }
  
    const {
      globalTranslations,
      originalFlagUrl,
      originalLanguageName
    } = window.MaxMenuConfig;
  
    const btnTranslate   = document.getElementById('BtnTranslateMenu');
    const modal          = document.getElementById('translateItemModalMenu');
    const closeBtn       = modal.querySelector('.close');
    const btnViewOriginal= document.getElementById('BtnViewOriginal');
    const idiomasContainer = document.getElementById('idiomasContainer');
  
    console.log("🔹 language.js cargado con MaxMenuConfig");
  
    // Guardamos textos originales
    document.querySelectorAll('[data-translate]').forEach(el => {
      el.setAttribute('data-original-text', el.textContent);
    });
  
    // Carga previa desde localStorage
    const savedLanguageId = localStorage.getItem('selectedLanguageId');
    const savedFlagUrl    = localStorage.getItem('selectedFlagUrl');
    if (savedLanguageId) {
      cambiarIdioma(savedLanguageId, savedFlagUrl, false);
    }
  
    // Abrir modal
    btnTranslate.addEventListener('click', () => {
      modal.style.display = 'block';
    });
  
    // Cerrar modal
    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
    });
  
    // Restaurar idioma original
    btnViewOriginal.addEventListener('click', e => {
      e.preventDefault();
      cargarIdiomaOriginal();
    });
  
    // Delegación para seleccionar idioma
    idiomasContainer.addEventListener('click', e => {
      const btn = e.target.closest('.idioma-btn');
      if (!btn) return;
      e.preventDefault();
      cambiarIdioma(
        btn.getAttribute('data-idioma'),
        btn.getAttribute('data-flag'),
        true
      );
    });
  
    // Funciones de traducción -----------------------
  
    function cambiarIdioma(languageId, flagUrl, closeModal = true) {
      console.log(`🔹 Cambiando idioma a: ${languageId}`);
      const data = globalTranslations[languageId];
      if (!data) {
        return console.error('❌ No hay traducciones para', languageId);
      }
  
      actualizarMenuConTraducciones(data);
  
      // Actualizar bandera
      const img = btnTranslate.querySelector('img');
      if (img && flagUrl) img.src = flagUrl;
  
      localStorage.setItem('selectedLanguageId', languageId);
      localStorage.setItem('selectedFlagUrl',    flagUrl);
  
      if (closeModal) modalClose();
    }
  
    function modalClose() {
      modal.style.display = 'none';
    }
  
    function cargarIdiomaOriginal() {
      console.log("🔹 Restaurando idioma original");
      document.querySelectorAll('[data-translate]').forEach(el => {
        const orig = el.getAttribute('data-original-text');
        if (orig != null) el.textContent = orig;
      });
      const img = btnTranslate.querySelector('img');
      if (img) {
        img.src = originalFlagUrl;
        img.alt = originalLanguageName;
      }
      localStorage.removeItem('selectedLanguageId');
      localStorage.removeItem('selectedFlagUrl');
      modalClose();
    }
  
    function actualizarMenuConTraducciones(data) {
      // Categorías
      (data.categories || []).forEach(cat => {
        const { category_id, translated_category_name, items, subcategories } = cat;
        // Nombre principal
        const elCat = document.querySelector(`[data-category-id="${category_id}"][data-translate="category"]`);
        if (elCat) elCat.textContent = translated_category_name;
        // Shortcut
        const elShort = document.querySelector(`#category-${category_id}-shortcut span[data-translate="category"]`);
        if (elShort) elShort.textContent = translated_category_name;
  
        // Ítems
        (items || []).forEach(item => {
          const node = document.querySelector(`[data-item-id="${item.item_id}"]`);
          if (!node) return;
          const t  = node.querySelector('.titulo');
          const d  = node.querySelector('.descripcion');
          if (t) t.textContent = item.translated_title;
          if (d) d.textContent = item.translated_description;
        });
  
        // Subcategorías
        (subcategories || []).forEach(sub => {
          const subEl = document.querySelector(`[data-subcategory-id="${sub.subcategory_id}"][data-translate="subcategory"]`);
          if (subEl) subEl.textContent = sub.translated_subcategory_name;
          const subShort = document.querySelector(`[data-subcategory-id="${sub.subcategory_id}"] span[data-translate="subcategory"]`);
          if (subShort) subShort.textContent = sub.translated_subcategory_name;
        });
      });
    }
  
    // Ya configurado, no seguimos esperando
  })();