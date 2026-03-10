import { Link } from "react-router-dom";

export default function ProductCard({ product, addToCart }) {

  const image = product.gallery?.[0];

  const handleQuickAdd = (e) => {
    e.preventDefault();

    const selectedAttributes = product.attributes?.map(attr => ({
      id: attr.id,
      value: attr.items[0].value
    }));

    addToCart({
      ...product,
      selectedAttributes
    });
  };

  return (
    <div className="product-card" data-testid="product-card">

      <Link to={`/product/${product.id}`}>

        <div className="product-image-wrapper">
          <img
            src={image}
            alt={product.name}
            className="product-image"
          />

          {!product.inStock && (
            <div className="out-of-stock">
              OUT OF STOCK
            </div>
          )}
        </div>

        <div className="product-info">
          <p className="product-name">{product.name}</p>

          <p className="product-price">
            ${Number(product.price).toFixed(2)}
          </p>
        </div>

      </Link>

      {product.inStock && (
        <button
          onClick={handleQuickAdd}
          className="quick-shop-btn"
        >
          +
        </button>
      )}

    </div>
  );
}