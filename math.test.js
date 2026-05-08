const sumarRuta = require('./math');

test('Suma correctamente 2 + 3 para igualar 5 km', () => {
  expect(sumarRuta(2, 3)).toBe(5);
});
