// script.js V15 - Completo com Suporte a Arquivos em Trilhas e Blog

document.addEventListener('DOMContentLoaded', function() {
    console.log('Plataforma Trilha V15 Carregada');
    
    carregarDadosDinamicos();

    const botaoRegularizar = document.getElementById('btn-regularizar');
    if (botaoRegularizar) {
        botaoRegularizar.addEventListener('click', function() {
             const secaoTrilhas = document.getElementById('trilhas');
             if (secaoTrilhas) secaoTrilhas.scrollIntoView({ behavior: 'smooth' });
        });
    }

    setupModal();
});

async function carregarDadosDinamicos() {
    try {
        const response = await fetch('admin/dados.json');
        if (!response.ok) throw new Error('Arquivo de dados não encontrado.');
        const data = await response.json();

        if (data.trilhas && data.trilhas.length > 0) renderizarTrilhas(data.trilhas);
        if (data.blog && data.blog.length > 0) renderizarBlog(data.blog);

    } catch (error) {
        console.warn('Usando conteúdo padrão do HTML (sem dados dinâmicos).');
    }
}

function renderizarTrilhas(trilhas) {
    const container = document.querySelector('.trilhas-grid-bw');
    if (!container) return;

    container.innerHTML = ''; 

    trilhas.forEach(item => {
        let btnHtml = '';
        let iconHtml = '';

        // Lógica do Ícone/Imagem
        if (item.imagem) {
            // Se tiver imagem de capa, mostra ela
            iconHtml = `<img src="admin/${item.imagem}" alt="${item.titulo}" style="width:70px; height:70px; border-radius:50%; object-fit:cover; margin-bottom:20px; border: 2px solid #000;">`;
        } else {
            // Se não, mostra o ícone padrão
            iconHtml = `<span class="trilha-icon"><i class="${item.icon || 'ri-file-list-line'}"></i></span>`;
        }

        // Lógica do Botão (Download ou Link)
        if (item.anexo) {
            // Se tiver arquivo anexado, botão baixa
            const anexoUrl = `admin/${item.anexo}`;
            btnHtml = `<a href="${anexoUrl}" class="btn-link-black" target="_blank" download>Baixar Material <i class="ri-download-line"></i></a>`;
        } else if (item.link) {
            // Se tiver link externo
            btnHtml = `<a href="${item.link}" class="btn-link-black" target="_blank">Acessar Link <i class="ri-external-link-line"></i></a>`;
        } else {
            // Padrão (modal)
            btnHtml = `<a href="#" class="btn-link-black js-open-modal">Ver Detalhes <i class="ri-arrow-right-line"></i></a>`;
        }

        const html = `
            <div class="trilha-card-bw">
                ${iconHtml}
                <h3>${item.titulo}</h3>
                <p>${item.desc}</p>
                ${btnHtml}
            </div>
        `;
        container.innerHTML += html;
    });
    setupModalLinks();
}

function renderizarBlog(posts) {
    const container = document.querySelector('.blog-grid-bw');
    if (!container) return;

    container.innerHTML = ''; 

    posts.slice().reverse().forEach(post => {
        let actionsHtml = '';
        
        if (post.anexo) {
            const anexoUrl = `admin/${post.anexo}`;
            let icon = 'ri-download-line';
            if(post.anexo.endsWith('.mp4')) icon = 'ri-play-circle-line';
            actionsHtml += `<a href="${anexoUrl}" class="btn-secondary-black" style="padding: 8px 15px; font-size: 0.8rem; margin-right: 10px;" target="_blank">Ver Anexo <i class="${icon}"></i></a>`;
        }

        if (post.link_externo) {
             actionsHtml += `<a href="${post.link_externo}" class="btn-link-black" target="_blank" style="font-size: 0.8rem;">Link Externo <i class="ri-external-link-line"></i></a>`;
        }

        const imagemUrl = post.imagem ? `admin/${post.imagem}` : 'https://via.placeholder.com/400x250?text=Sem+Imagem';

        const html = `
            <div class="trilha-card-bw" style="text-align: left; padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                <div style="height: 200px; overflow: hidden; border-bottom: 1px solid #eee;">
                    <img src="${imagemUrl}" alt="${post.titulo}" style="width: 100%; height: 100%; object-fit: cover; filter: grayscale(100%); transition: filter 0.3s;">
                </div>
                <div style="padding: 30px; flex-grow: 1; display: flex; flex-direction: column;">
                    <small style="color: #777; display: block; margin-bottom: 10px;">${post.data || 'Data não inf.'}</small>
                    <h3 style="margin-bottom: 15px; font-size: 1.4rem;">${post.titulo}</h3>
                    <p style="flex-grow: 1;">${post.resumo}</p>
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                        ${actionsHtml || '<small>Leitura rápida</small>'}
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += html;
    });
    
    const style = document.createElement('style');
    style.innerHTML = `.blog-grid-bw .trilha-card-bw:hover img { filter: grayscale(0%) !important; }`;
    document.head.appendChild(style);
}

function setupModal() {
    const modal = document.getElementById('modal-em-breve');
    const btnCloseModal = document.getElementById('btn-close-modal');

    if (btnCloseModal && modal) {
        btnCloseModal.addEventListener('click', () => { modal.classList.remove('active'); });
        modal.addEventListener('click', (event) => { if (event.target === modal) modal.classList.remove('active'); });
    }
    setupModalLinks();
}

function setupModalLinks() {
    const modal = document.getElementById('modal-em-breve');
    const linksModal = document.querySelectorAll('.js-open-modal');
    
    linksModal.forEach(link => {
        link.removeEventListener('click', openModalHandler);
        link.addEventListener('click', openModalHandler);
    });

    function openModalHandler(event) {
        event.preventDefault();
        if (modal) modal.classList.add('active');
    }
}