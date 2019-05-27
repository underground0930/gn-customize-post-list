const { Component, render, Fragment } = wp.element; // wp.elementにReact関連が格納されている。
import '../css/style.css';
import deepcopy from 'deepcopy';
import axios from 'axios';
import qs from 'qs';

const OPTIONS = [
  { key: 'title', label: 'title' },
  { key: 'content', label: 'content' },
  { key: 'custom_field', label: 'custom field' },
  { key: 'taxonomy', label: 'taxonomy' },
  { key: 'date', label: 'date' }
];
const DEFAULT_OPTION = {
  key: 'title',
  value: null
};
const DEFAULT_OPTIONS = [
  DEFAULT_OPTION,
  {
    key: 'date',
    value: null
  }
];
const SELECT_MAX_LENGTH = 5;

class App extends Component {
  constructor() {
    super();
    this.state = {
      types: window.gncpl_admin_post_types,
      options: window.gncpl_admin_options
    };
    this.add = this.add.bind(this);
    this.deleteColumn = this.deleteColumn.bind(this);
    this.updateText = this.updateText.bind(this);
    this.updateSelect = this.updateSelect.bind(this);
    this.resetSelect = this.resetSelect.bind(this);
    this.checkSelectType = this.checkSelectType.bind(this);
    this.updateOptions = this.updateOptions.bind(this);
  }
  componentWillMount() {
    this.init();
  }
  init() {
    let newOptions = {};
    this.state.types.forEach(item => {
      if (!this.state.options[item.name]) {
        newOptions[item.name] = deepcopy(DEFAULT_OPTIONS);
      }
    });
    this.setState(prevState => {
      return { options: Object.assign({}, prevState.options, newOptions) };
    });
  }
  add(type) {
    if (this.state.options[type].length >= SELECT_MAX_LENGTH) return;
    this.setState( prevState =>{
      prevState.options[type].push({...DEFAULT_OPTION})
      return {
        options: prevState.options
      }
    })
  }
  deleteColumn(type, i) {
    this.setState(prevState => {
      prevState.options[type].splice(i, 1);
      return {
        options: prevState.options
      };
    });
  }
  updateText(e, type, i) {
    const value = e.target.value;
    this.setState(prevState => {
      prevState.options[type][i]['value'] = value;
      return {
        options: prevState.options
      };
    });

    this.setState({
      options: this.state.options
    });
  }
  updateSelect(e, type, i) {
    let val = e.target.value;
    this.setState(prevState => {
      prevState.options[type][i]['key'] = val;
      if (!this.checkSelectType) {
        prevState.options[type][i]['value'] = null;
      }
      return {
        options: prevState.options
      };
    });
  }
  resetSelect(type) {
    this.setState(prevState => {
      prevState.options[type] = deepcopy(DEFAULT_OPTIONS);
      return {
        options: prevState.options
      };
    });
  }
  checkSelectType(slug) {
    return slug === 'taxonomy' || slug === 'custom_field' ? true : false;
  }
  updateOptions() {

    // let params = new URLSearchParams();
    // params.append('action', 'abcde');
    // params.append('security', window.security);
    // params.append('gncpl_options', this.state.options);    

    const data = {
        action: 'abcde',
        security: window.security,
        gncpl_options: this.state.options
      };

    const options = {
      method: 'POST',
      data: qs.stringify(data),
      url : window.admin_ajax_url
    }

    axios(options)
    .then(function (response) {
      console.log(response);
    })
    .catch(function (error) {
      console.log(error);
    });
  }
  render() {
    return (
      <Fragment>
        {this.state.types.map(type => (
          <section className="gncpl-admin-section" key={type.name}>
            <h4>【{type.label}】</h4>
            <ul className="gncpl-admin-list">
              {this.state.options[type.name] &&
                this.state.options[type.name].map((v, i) => {
                  return (
                    <li className="gncpl-admin-listChild" key={i}>
                      <b>key :</b>
                      <select
                        className="gncpl-admin-listSelect"
                        value={v['key']}
                        onChange={e => {
                          this.updateSelect(e, type.name, i);
                        }}
                      >
                        {OPTIONS.map(item => {
                          return (
                            <option key={item.key} value={item.key}>
                              {item.label}
                            </option>
                          );
                        })}
                      </select>
                      {(() => {
                        if (this.checkSelectType(v.key)) {
                          return (
                            <div>
                              <b>val : </b>
                              <input
                                className="gncpl-admin-input"
                                type="text"
                                value={v.value}
                                placeholder="please input slug"
                                onChange={e => {
                                  this.updateText(e, type.name, i);
                                }}
                              />
                            </div>
                          );
                        }
                      })()}
                      <p className="gncpl-admin-litDeleteWrap">
                        <button
                          onClick={e => {
                            e.preventDefault();
                            this.deleteColumn(type.name, i);
                          }}
                          className="button btn-danger button-large gncpl-admin-listDelete"
                        >
                          削除
                        </button>
                      </p>
                    </li>
                  );
                })}
            </ul>
            <button
              class="gncpl-admin-listAdd gncpl-admin-btn button button-primary button-large"
              onClick={e => {
                e.preventDefault();
                this.add(type.name);
              }}
            >
              追加
            </button>
            <button
              class="gncpl-admin-listReset gncpl-admin-btn button button button-primary button-large"
              onClick={e => {
                e.preventDefault();
                this.resetSelect(type.name);
              }}
            >
              リセット
            </button>
          </section>
        ))}
        <input type="submit" value="update" onClick={this.updateOptions} />
      </Fragment>
    );
  }
}

render(<App />, document.getElementById('gncpl-admin-app'));
