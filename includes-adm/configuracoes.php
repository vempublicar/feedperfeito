<div class="bg-white rounded-lg shadow-md p-6">
  <h2 class="text-2xl font-bold text-black mb-6">Configurações</h2>
  
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
      <nav class="space-y-1">
        <a href="#" class="bg-gray-100 text-black group flex items-center px-3 py-2 text-sm font-medium rounded-md">
          <i class="fas fa-cog mr-3 text-gray-500"></i>
          <span>Geral</span>
        </a>
        <a href="#" class="text-gray-600 hover:bg-gray-50 hover:text-black group flex items-center px-3 py-2 text-sm font-medium rounded-md">
          <i class="fas fa-palette mr-3 text-gray-500"></i>
          <span>Aparência</span>
        </a>
        <a href="#" class="text-gray-600 hover:bg-gray-50 hover:text-black group flex items-center px-3 py-2 text-sm font-medium rounded-md">
          <i class="fas fa-envelope mr-3 text-gray-500"></i>
          <span>E-mail</span>
        </a>
        <a href="#" class="text-gray-600 hover:bg-gray-50 hover:text-black group flex items-center px-3 py-2 text-sm font-medium rounded-md">
          <i class="fas fa-credit-card mr-3 text-gray-500"></i>
          <span>Pagamentos</span>
        </a>
        <a href="#" class="text-gray-600 hover:bg-gray-50 hover:text-black group flex items-center px-3 py-2 text-sm font-medium rounded-md">
          <i class="fas fa-users mr-3 text-gray-500"></i>
          <span>Usuários</span>
        </a>
        <a href="#" class="text-gray-600 hover:bg-gray-50 hover:text-black group flex items-center px-3 py-2 text-sm font-medium rounded-md">
          <i class="fas fa-shield-alt mr-3 text-gray-500"></i>
          <span>Segurança</span>
        </a>
      </nav>
    </div>
    
    <div class="lg:col-span-2">
      <div class="border-b border-gray-200 pb-5 mb-6">
        <h3 class="text-lg font-medium text-black">Configurações Gerais</h3>
        <p class="mt-1 text-sm text-gray-500">Gerencie as configurações básicas do sistema</p>
      </div>
      
      <form class="space-y-6">
        <div>
          <label for="nome_empresa" class="block text-sm font-medium text-gray-700">Nome da Empresa</label>
          <input type="text" name="nome_empresa" id="nome_empresa" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" value="FeedPerfeito">
        </div>
        
        <div>
          <label for="email_contato" class="block text-sm font-medium text-gray-700">E-mail de Contato</label>
          <input type="email" name="email_contato" id="email_contato" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" value="contato@feedperfeito.com">
        </div>
        
        <div>
          <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
          <input type="text" name="telefone" id="telefone" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" value="(11) 99999-9999">
        </div>
        
        <div>
          <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
          <textarea id="endereco" name="endereco" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">Av. Paulista, 1000 - São Paulo/SP</textarea>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Logo</label>
          <div class="mt-1 flex items-center">
            <div class="w-16 h-16 rounded-md bg-gray-200 flex items-center justify-center">
              <i class="fas fa-image text-gray-500"></i>
            </div>
            <div class="ml-5">
              <div class="flex text-sm text-gray-600">
                <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-black hover:text-gray-700">
                  <span>Upload de arquivo</span>
                  <input id="logo" name="logo" type="file" class="sr-only">
                </label>
                <p class="pl-1">ou arraste e solte</p>
              </div>
              <p class="text-xs text-gray-500">PNG, JPG até 2MB</p>
            </div>
          </div>
        </div>
        
        <div class="flex items-center">
          <input id="manutencao" name="manutencao" type="checkbox" class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded">
          <label for="manutencao" class="ml-2 block text-sm text-gray-700">Modo de manutenção</label>
        </div>
        
        <div class="flex justify-end">
          <button type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
            Cancelar
          </button>
          <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
            Salvar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>