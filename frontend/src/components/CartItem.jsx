export default function CartItem({ item, changeQuantity }) {
  const image = item.product.gallery?.[0] || "/placeholder.png";

  const getSelectedValue = (attrName) => {
    return item.selectedAttributes?.find(a => a.name === attrName)?.value;
  };

  return (
    <div data-testid="cart-item" className="cart-item">
      <div className="cart-item-info">
        <p className="cart-item-name">{item.product.name}</p>
        <p className="cart-item-price">
          ${item.price?.toFixed(2) ?? item.product?.price?.toFixed(2) ?? "0.00"}
        </p>

        {item.product.attributes?.map((attr) => {
          const selectedValue = getSelectedValue(attr.name);

          return (
            <div key={attr.name} className="cart-item-attribute-block">
              <p className="cart-item-attribute-label">
                {attr.name.charAt(0).toUpperCase() + attr.name.slice(1)}:
              </p>

              <div className="cart-item-attribute-values">
                {attr.type === "swatch"
                  ? attr.items.map((option) => {
                      const isSelected = selectedValue === option.value;
                      return (
                        <div
                          key={option.value}
                          className={`cart-item-color-swatch ${isSelected ? "selected" : ""}`}
                          style={{ backgroundColor: option.value }}
                        />
                      );
                    })
                  : attr.items.map((option) => {
                      const isSelected = selectedValue === option.value;
                      return (
                        <div
                          key={option.value}
                          className={`cart-item-attribute-btn ${isSelected ? "selected" : ""}`}
                        >
                          {option.displayValue || option.value}
                        </div>
                      );
                    })}
              </div>
            </div>
          );
        })}
      </div>

      {/* QUANTITY */}
      <div className="cart-item-quantity">
        <button
          className="cart-qty-btn"
          onClick={() =>
            changeQuantity(item.product.id, item.selectedAttributes, 1)
          }
        >
          +
        </button>

        <span>{item.quantity}</span>

        <button
          className="cart-qty-btn"
          onClick={() =>
            changeQuantity(item.product.id, item.selectedAttributes, -1)
          }
        >
          −
        </button>
      </div>

      {/* IMAGE */}
      <div className="cart-item-image-wrapper">
        <img src={image} alt={item.product.name} className="cart-item-image" />
      </div>
    </div>
  );
}