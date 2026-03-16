export default function CartItem({ item, changeQuantity }) {
  const image = item.product.gallery?.[0] || "/placeholder.png";

  return (
    <div data-testid="cart-item" className="cart-item">
      <div className="cart-item-info">
        <p className="cart-item-name">{item.product.name}</p>
        <p className="cart-item-price">${item.product.price.toFixed(2)}</p>

        {item.product.attributes?.map((attr) => {
          const selectedValue = item.selectedAttributes?.find(a => a.id === attr.name)?.value;

          return (
            <div key={attr.name} className="cart-item-attribute-block">
              <p className="attribute-label">{attr.name}:</p>
              <div className="attribute-options">
                {attr.type === "swatch"
                  ? attr.items.map((option) => (
                      <button
                        key={option.value}
                        className={`color-swatch ${
                          selectedValue === option.value ? 'selected' : ''
                        }`}
                        style={{ backgroundColor: option.value }}
                        disabled
                      />
                    ))
                  : attr.items.map((option) => (
                      <button
                        key={option.value}
                        className={`attribute-btn ${
                          selectedValue === option.value ? 'selected' : ''
                        }`}
                        disabled
                      >
                        {option.displayValue || option.value}
                      </button>
                    ))
                }
              </div>
            </div>
          );
        })}
      </div>

      <div className="cart-item-quantity">
        <button
          data-testid="cart-item-amount-increase"
          className="cart-qty-btn"
          onClick={() => changeQuantity(item.product.id, item.selectedAttributes, 1)}
        >
          +
        </button>

        <span data-testid="cart-item-amount">{item.quantity}</span>

        <button
          data-testid="cart-item-amount-decrease"
          className="cart-qty-btn"
          onClick={() => changeQuantity(item.product.id, item.selectedAttributes, -1)}
        >
          −
        </button>
      </div>

      <div className="cart-item-image-wrapper">
        <img src={image} alt={item.product.name} className="cart-item-image" />
      </div>
    </div>
  );
}