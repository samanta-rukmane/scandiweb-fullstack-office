export default function CartItem({ item, changeQuantity }) {
  const kebabName = item.product.name.toLowerCase().replace(/\s+/g, '-');
  const image = item.product.gallery?.[0] || "/placeholder.png";

  return (
    <div
      data-testid={`cart-item-${kebabName}`}
      className="flex justify-between items-start mb-4 border-b pb-4"
    >
      <img src={image} alt={item.product.name} className="w-24 h-24 object-cover rounded" />

      <div className="flex-1 ml-4 flex flex-col justify-between">
        <div>
          <p className="font-medium">{item.product.name}</p>
          <p className="text-sm text-gray-600">${item.product.price.toFixed(2)}</p>

          {item.product.selectedAttributes?.map(attr => (
            <div
              key={attr.id}
              data-testid={`cart-item-attribute-${attr.id}`}
              className="flex items-center gap-2 mt-1"
            >
              <span className="font-semibold text-sm">{attr.id}:</span>
              {attr.type === "swatch" ? (
                <div className="flex gap-1">
                  <span
                    className={`w-5 h-5 border rounded ${attr.value === attr.selected ? "ring-2 ring-green-500" : ""}`}
                    style={{ backgroundColor: attr.value }}
                  ></span>
                </div>
              ) : (
                <span className="text-sm font-medium">{attr.value}</span>
              )}
            </div>
          ))}
        </div>

        <div className="flex items-center gap-2 mt-2">
          <button
            data-testid="cart-item-amount-decrease"
            className="bg-gray-200 w-6 h-6 rounded flex items-center justify-center"
            onClick={() => changeQuantity(item.product.id, -1)}
          >
            -
          </button>

          <span data-testid="cart-item-amount" className="text-center w-6">
            {item.quantity}
          </span>

          <button
            data-testid="cart-item-amount-increase"
            className="bg-gray-200 w-6 h-6 rounded flex items-center justify-center"
            onClick={() => changeQuantity(item.product.id, 1)}
          >
            +
          </button>
        </div>
      </div>
    </div>
  );
}