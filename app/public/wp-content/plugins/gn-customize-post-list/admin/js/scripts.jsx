(() => {
  const { Component, render, Fragment } = wp.element; // wp.elementにReact関連が格納されている。

  class App extends Component {
    constructor() {
      super();
      this.state = {
        lists: window.gcpl_admin_json
      };
    }
    componentDidMount() {}
    render() {
      return (
          <Fragment>
          {this.state.lists.map(v => (
            <section key={v.label}>
              <h4>{v.label}</h4>
              <div>{v.name}</div>
            </section>
          ))}
          </Fragment>
      );
    }
  }

  render(<App />, document.getElementById('gcpl-admin-app'));
})();
