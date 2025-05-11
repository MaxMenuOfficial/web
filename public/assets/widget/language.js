(function waitForMaxMenuConfigAndDOM() {
    if (
      typeof window.MaxMenuConfig === 'undefined' ||
      !document.getElementById('maxmenu-menuContainer')
    ) {
      return requestAnimationFrame(waitForMaxMenuConfigAndDOM);
    }
  
    const { globalTranslations, originalFlagUrl, originalLanguageName } = window.MaxMenuConfig;
  
    const modal = document.getElementById("translateItemModalMenu");
    const closeBtn = modal?.querySelector(".close");
    const btnTranslate = document.getElementById("BtnTranslateMenu");
    const btnViewOriginal = document.getElementById("BtnViewOriginal");
    const idiomasContainer = document.getElementById("idiomasContainer");
  
    if (!modal || !btnTranslate || !closeBtn || !btnViewOriginal || !idiomasContainer) {
      console.warn("[MaxMenu] Elementos del selector de idioma no encontrados");
      return;
    }
  
    console.log("🔹 MaxMenu Language Script Ready");
  
    // Guardar textos originales
    document.querySelectorAll("[data-translate]").forEach(el => {
      el.setAttribute("data-original-text", el.textContent);
    });
  
    // Cargar idioma guardado
    const savedLanguageId = localStorage.getItem('selectedLanguageId');
    const savedFlagUrl = localStorage.getItem('selectedFlagUrl');
  
    if (savedLanguageId && globalTranslations[savedLanguageId]) {
      cambiarIdioma(savedLanguageId, savedFlagUrl, false);
    }
  
    // Eventos UI
    btnTranslate.addEventListener('click', () => {
      modal.style.display = 'block';
    });
  
    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
    });
  
    btnViewOriginal.addEventListener('click', (e) => {
      e.preventDefault();
      cargarIdiomaOriginal();
    });
  
    idiomasContainer.addEventListener('click', (e) => {
      const button = e.target.closest('.idioma-btn');
      if (!button) return;
      e.preventDefault();
      const langId = button.getAttribute('data-idioma');
      const flag = button.getAttribute('data-flag');
      cambiarIdioma(langId, flag);
    });
  
    function cambiarIdioma(languageId, flagUrl, closeModal = true) {
      if (!globalTranslations[languageId]) {
        console.error('❌ Traducción no encontrada:', languageId);
        return;
      }
  
      actualizarMenuConTraducciones(globalTranslations[languageId]);
  
      const flagImg = document.querySelector('#BtnTranslateMenu img');
      if (flagImg) flagImg.src = flagUrl;
  
      localStorage.setItem('selectedLanguageId', languageId);
      localStorage.setItem('selectedFlagUrl', flagUrl);
  
      if (closeModal) modalClose();
    }
  
    function cargarIdiomaOriginal() {
      document.querySelectorAll("[data-translate]").forEach(el => {
        const original = el.getAttribute("data-original-text");
        if (original) el.textContent = original;
      });
  
      const flagImg = document.querySelector('#BtnTranslateMenu img');
      if (flagImg) {
        flagImg.src = originalFlagUrl;
        flagImg.alt = originalLanguageName;
      }
  
      localStorage.removeItem('selectedLanguageId');
      localStorage.removeItem('selectedFlagUrl');
  
      modalClose();
    }
  
    function modalClose() {
      modal.style.display = 'none';
    }
  
    function actualizarMenuConTraducciones(data) {
      if (!Array.isArray(data.categories)) {
        console.error('❌ Formato de datos inválido:', data);
        return;
      }
  
      data.categories.forEach(cat => {
        const { category_id, translated_category_name, subcategories, items } = cat;
  
        document.querySelectorAll(`[data-category-id="${category_id}"][data-translate="category"]`)?.forEach(el => el.textContent = translated_category_name);
        document.querySelector(`#category-${category_id}-shortcut span[data-translate="category"]`)?.textContent = translated_category_name;
  
        subcategories?.forEach(sub => {
          const { subcategory_id, translated_subcategory_name } = sub;
          document.querySelectorAll(`[data-subcategory-id="${subcategory_id}"][data-translate="subcategory"]`)?.forEach(el => el.textContent = translated_subcategory_name);
          document.querySelector(`[data-subcategory-id="${subcategory_id}"] span[data-translate="subcategory"]`)?.textContent = translated_subcategory_name;
        });
  
        items?.forEach(item => {
          const { item_id, translated_title, translated_description } = item;
          const itemEl = document.querySelector(`[data-item-id="${item_id}"]`);
          itemEl?.querySelector(".titulo")?.textContent = translated_title;
          itemEl?.querySelector(".descripcion")?.textContent = translated_description;
        });
      });
    }
  })();