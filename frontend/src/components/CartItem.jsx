export default function CartItem({ item, changeQuantity }) {
  const image = item.product.gallery?.[0] || "/placeholder.png";

  return (
    <div data-testid="cart-item" className="cart-item">
      
      <div className="cart-item-info">
        <p className="cart-item-name">{item.product.name}</p>
        <p className="cart-item-price">${item.product.price.toFixed(2)}</p>

        {item.product.selectedAttributes?.map(attr => (
          <div key={attr.name} className="cart-item-attribute">
            <span className="cart-item-attribute-name">{attr.name}:</span>
            <span
              data-testid="cart-item-attribute-selected"
              className="cart-item-attribute-value"
            >
              {attr.value}
            </span>
          </div>
        ))}
      </div>

      <div className="cart-item-quantity">
        <button
          data-testid="cart-item-amount-increase"
          className="cart-qty-btn"
          onClick={() => changeQuantity(item.product.id, item.product.selectedAttributes, 1)}
        >
          +
        </button>

        <span data-testid="cart-item-amount">{item.quantity}</span>

        <button
          data-testid="cart-item-amount-decrease"
          className="cart-qty-btn"
          onClick={() => changeQuantity(item.product.id, item.product.selectedAttributes, -1)}
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