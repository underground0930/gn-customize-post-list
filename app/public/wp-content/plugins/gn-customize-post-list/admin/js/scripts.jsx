const { Component, render, Fragment } = wp.element; // wp.elementにReact関連が格納されている。
import '../css/style.css';

const OPTIONS = [
  { key: 'title', label: 'title' },
  { key: 'content', label: 'content' },
  { key: 'custom_field', label: 'custom field' },
  { key: 'taxonomy', label: 'taxonomy' },
  { key: 'date', label: 'date' }
];
const SELECT_MAX_LENGTH = 5;

class App extends Component {
  constructor() {
    super();
    this.state = {
      types: window.gncpl_admin_post_types,
      // options: window.gncpl_admin_options,
      options: {
        post: [
          {
            key: 'title',
            key: null,
          },
          {
            key: 'content'
          },
          {
            key: 'date'
          },
          {
            key: 'custom_field',
            value: 1
          },
          {
            key: 'taxonomy',
            value: 2
          }
        ],
        news: [
          {
            key: 'taxonomy',
            value: 2
          }
        ],
        works: [
          {
            key: 'taxonomy',
            value: 2
          }
        ]
      }
    };
    this.add = this.add.bind(this);
    this.delete = this.delete.bind(this);
    this.updateText = this.updateText.bind(this);
    this.updateSelect = this.updateSelect.bind(this);
    this.checkSelectType = this.checkSelectType.bind(this);
  }
  componentDidMount() {}
  add(type){
    if(this.state.options[type].length >= SELECT_MAX_LENGTH) return;
    const defaultObj = {
      key: 'title'
    }
    this.state.options[type].push(defaultObj);
    this.setState({
      options: this.state.options
    })
  }
  delete(type, i) {
    this.state.options[type].splice(i, 1);
    this.setState({
      options: this.state.options
    });
  }
  updateText(e,type,i){
    this.state.options[type][i]['value'] = e.target.value;
    this.setState({
      options: this.state.options
    })
  }
  updateSelect(e,type,i){
    this.state.options[type][i]['key'] = e.target.value;
    if(!this.checkSelectType(e.target.value)){
      this.state.options[type][i]['value'] = null;
    }
    this.setState({
      options: this.state.options
    })
  }
  checkSelectType(slug){
    return (slug === 'taxonomy' || slug === 'custom_field') ? true : false;
  }
  render() {
    return (
      <Fragment>
        {this.state.types.map(type => (
          <section className="gncpl-admin-section" key={type.name}>
            <h4>【{type.label}】</h4>
            <ul className="gncpl-admin-list">
              {this.state.options[type.name].map((v, i) => {
                return (
                  <li className="gncpl-admin-listChild" key={i}>
                    <select
                      className="gncpl-admin-listSelect"
                      value={v['key']}
                      onChange={(e)=>{
                        this.updateSelect(e,type.name,i);
                      }}
                    >
                      {OPTIONS.map(item => {
                        return (
                          <option
                            key={item.key}
                            value={item.key}
                          >
                            {item.label}
                          </option>
                        );
                      })}
                    </select>
                    {(() => {
                      if (this.checkSelectType(v.key)) {
                        return <div><input type="text" value={v.value} placeholder="please input slug" onChange={(e)=>{
                          this.updateText(e,type.name,i);
                        }} /></div>
                      }
                    })()}
                    <button
                      onClick={e => {
                        e.preventDefault();
                        this.delete(type.name, i);
                      }}
                      className="button btn-danger button-large gncpl-admin-listDelete"
                    >
                      削除
                    </button>
                  </li>
                );
              })}
            </ul>
            <button class="gncpl-admin-listAdd button button-primary button-large" onClick={(e=>{
              e.preventDefault();
              this.add(type.name);
            })}>
              追加
            </button>
          </section>
        ))}
      </Fragment>
    );
  }
}

render(<App />, document.getElementById('gncpl-admin-app'));
