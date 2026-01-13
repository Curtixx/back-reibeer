# Guia de Uso do Sistema de Logs

Sistema genérico de logs de atividades para rastrear ações de usuários no sistema.

## Estrutura

### Tabela: `activity_logs`
- `user_id`: ID do usuário que executou a ação
- `model_type`: Tipo do modelo (classe completa)
- `model_id`: ID do registro
- `action`: Tipo de ação (created, updated, deleted, custom)
- `column_name`: Nome da coluna alterada (opcional)
- `old_value`: Valor anterior (opcional)
- `new_value`: Valor novo (opcional)
- `description`: Descrição da ação
- `created_at`: Timestamp da ação

## Como Usar

### 1. Logs Automáticos com Trait

Para logar automaticamente todas as mudanças em um modelo, adicione o trait `LogsActivity`:

```php
use App\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;
    
    // Seus outros códigos...
}
```

Isso irá automaticamente logar:
- **Criação** do registro
- **Atualização** de campos (cada campo alterado gera um log)
- **Exclusão** do registro

### 2. Logs Manuais no Modelo

Se você adicionou o trait, pode criar logs manuais usando o método `logActivity`:

```php
$product = Product::find(1);

// Log simples
$product->logActivity('price_changed', 'Preço alterado manualmente');

// Log com detalhes
$product->logActivity(
    action: 'discount_applied',
    description: 'Desconto de 10% aplicado',
    columnName: 'sale_price',
    oldValue: 100,
    newValue: 90
);
```

### 3. Logs com o LogService

Use o `LogService` para criar logs sem precisar do trait ou para logs mais complexos:

```php
use App\Services\LogService;

$logService = new LogService();

// Log vinculado a um modelo
$product = Product::find(1);
$logService->log(
    model: $product,
    action: 'stock_alert',
    description: 'Estoque baixo detectado'
);

// Log genérico do sistema (sem modelo específico)
$logService->logGeneric(
    action: 'backup_created',
    description: 'Backup automático criado com sucesso'
);
```

### 4. Consultando Logs

#### Através do Modelo (com trait)
```php
$product = Product::find(1);

// Todos os logs do produto
$logs = $product->activityLogs;

// Logs do produto com usuário
$logs = $product->activityLogs()->with('user')->get();
```

#### Através do LogService
```php
$logService = new LogService();

// Logs de um modelo específico
$logs = $logService->getLogsFor($product);

// Logs de um usuário
$logs = $logService->getLogsByUser($userId);

// Logs por tipo de ação
$logs = $logService->getLogsByAction('created');
```

#### Direto no Modelo ActivityLog
```php
use App\Models\ActivityLog;

// Logs recentes
$logs = ActivityLog::with('user')
    ->orderBy('created_at', 'desc')
    ->limit(100)
    ->get();

// Logs de um período
$logs = ActivityLog::whereBetween('created_at', [$inicio, $fim])
    ->with('user')
    ->get();

// Logs por modelo
$logs = ActivityLog::where('model_type', Product::class)
    ->where('action', 'updated')
    ->get();
```

## Exemplos Práticos

### Exemplo 1: Produto com Logs Automáticos
```php
use App\Models\Product;
use App\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;
}

// Ao salvar, tudo é logado automaticamente
$product = Product::create(['name' => 'Cerveja', 'price' => 10]);
// Log: action='created', description='Registro criado'

$product->update(['price' => 12]);
// Log: action='updated', column_name='price', old_value='10', new_value='12'
```

### Exemplo 2: StockService com Logs Manuais
```php
use App\Services\LogService;

class StockService
{
    public function __construct(
        protected LogService $logService
    ) {}
    
    public function addQuantity(int $productId, int $quantity): Stock
    {
        $stock = Stock::find($productId);
        $oldQty = $stock->quantity;
        
        $stock->increment('quantity', $quantity);
        
        // Log manual da operação
        $this->logService->log(
            model: $stock,
            action: 'quantity_added',
            description: "Adicionado {$quantity} unidades ao estoque",
            columnName: 'quantity',
            oldValue: $oldQty,
            newValue: $stock->quantity
        );
        
        return $stock;
    }
}
```

### Exemplo 3: Logs de Sistema
```php
// No seu CashierController
public function close(Request $request, LogService $logService)
{
    $cashier = Cashier::find($request->cashier_id);
    $cashier->update(['closed_at' => now()]);
    
    // Log genérico do sistema
    $logService->logGeneric(
        action: 'cashier_closed',
        description: "Caixa #{$cashier->id} fechado com total de R$ {$request->total_sales}"
    );
}
```

## Desabilitando Logs Automáticos Temporariamente

Se precisar fazer operações sem logar (ex: seeders, importações em massa):

```php
// Opção 1: Não usar o trait LogsActivity no modelo durante a operação

// Opção 2: Criar modelo temporário sem trait
class ProductWithoutLogs extends Product
{
    use HasFactory;
    // Sem LogsActivity
}
```

## Migration

A migration já foi criada. Para executar quando o banco estiver configurado:

```bash
php artisan migrate
```

## Considerações

- Os logs **não têm updated_at**, apenas **created_at** (são imutáveis)
- Timestamps automáticos (created_at, updated_at) não são logados nas mudanças
- Valores complexos (arrays, objetos) são serializados como JSON
- O user_id pode ser nulo (para operações do sistema)
- Use índices para consultas rápidas (já criados na migration)
